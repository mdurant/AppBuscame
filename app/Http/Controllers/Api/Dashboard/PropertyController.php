<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Enums\PropertyStatus;
use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use App\Models\Property\PropertyAddress;
use App\Models\Property\Source;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Property::where('user_id', $request->user()->id)
            ->with(['address', 'photos'])
            ->latest();

        $perPage = min($request->integer('per_page', 15), 50);
        $items = $query->paginate($perPage);

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:casa,departamento,habitacion,estudio,otro'],
            'rental_type' => ['nullable', 'string', 'max:30'],
            'cost_amount' => ['nullable', 'numeric', 'min:0'],
            'cost_currency' => ['nullable', 'string', 'size:3'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'includes_cleaning' => ['boolean'],
            'address_line' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'region' => ['nullable', 'string'],
            'country' => ['nullable', 'string', 'size:2'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'place_id' => ['nullable', 'string'],
        ]);

        $platformSource = Source::where('slug', 'buscame')->first();

        $property = Property::create([
            'user_id' => $request->user()->id,
            'source_id' => $platformSource?->id,
            'type' => $validated['type'],
            'status' => PropertyStatus::Draft,
            'rental_type' => $validated['rental_type'] ?? null,
            'cost_amount' => $validated['cost_amount'] ?? null,
            'cost_currency' => $validated['cost_currency'] ?? 'CLP',
            'check_in_time' => $validated['check_in_time'] ?? null,
            'check_out_time' => $validated['check_out_time'] ?? null,
            'includes_cleaning' => $validated['includes_cleaning'] ?? false,
            'completeness_percent' => 0,
        ]);

        if (! empty($validated['address_line']) || ! empty($validated['latitude'])) {
            PropertyAddress::create([
                'property_id' => $property->id,
                'address_line' => $validated['address_line'] ?? null,
                'city' => $validated['city'] ?? null,
                'region' => $validated['region'] ?? null,
                'country' => $validated['country'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'place_id' => $validated['place_id'] ?? null,
            ]);
        }

        $this->updateCompleteness($property);

        return response()->json($property->load('address'), 201);
    }

    public function show(Request $request, Property $property): JsonResponse
    {
        $this->authorize('view', $property);
        $property->load(['address', 'photos', 'availabilityRanges', 'score']);

        return response()->json($property);
    }

    public function update(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $validated = $request->validate([
            'type' => ['sometimes', 'string', 'in:casa,departamento,habitacion,estudio,otro'],
            'rental_type' => ['nullable', 'string', 'max:30'],
            'cost_amount' => ['nullable', 'numeric', 'min:0'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'includes_cleaning' => ['boolean'],
            'address_line' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'place_id' => ['nullable', 'string'],
        ]);

        $property->update(array_filter([
            'type' => $validated['type'] ?? null,
            'rental_type' => $validated['rental_type'] ?? null,
            'cost_amount' => $validated['cost_amount'] ?? null,
            'check_in_time' => $validated['check_in_time'] ?? null,
            'check_out_time' => $validated['check_out_time'] ?? null,
            'includes_cleaning' => $validated['includes_cleaning'] ?? null,
        ]));

        $addr = $property->address;
        if ($addr) {
            $addr->update(array_filter([
                'address_line' => $validated['address_line'] ?? null,
                'city' => $validated['city'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'place_id' => $validated['place_id'] ?? null,
            ]));
        }

        $this->updateCompleteness($property);

        return response()->json($property->fresh(['address', 'photos']));
    }

    public function publish(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        if ($property->status !== PropertyStatus::Draft) {
            return response()->json(['message' => 'Solo se pueden publicar borradores.'], 422);
        }

        if ($property->completeness_percent < 100) {
            return response()->json([
                'message' => 'Completa todos los campos obligatorios y sube al menos una foto para publicar.',
                'completeness_percent' => $property->completeness_percent,
            ], 422);
        }

        $hasActiveSubscription = $request->user()->subscriptions()->where('status', 'active')->where('ends_at', '>', now())->exists();
        if (! $hasActiveSubscription) {
            $property->update(['status' => PropertyStatus::PendingPayment]);

            return response()->json([
                'message' => 'Para publicar necesitas una suscripción activa.',
                'redirect_to_payment' => true,
                'property' => $property->fresh(),
            ], 402);
        }

        $property->update([
            'status' => PropertyStatus::Published,
            'published_at' => now(),
        ]);

        event(new \App\Events\PropertyPublished($property));

        return response()->json([
            'message' => 'Aviso publicado correctamente.',
            'property' => $property->fresh(['address', 'photos']),
        ]);
    }

    private function updateCompleteness(Property $property): void
    {
        $score = 0;
        $total = 0;
        if ($property->type) { $total++; $score++; }
        if ($property->cost_amount !== null) { $total++; $score++; }
        if ($property->check_in_time) { $total++; $score++; }
        if ($property->check_out_time) { $total++; $score++; }
        $total++; $score += $property->address ? 1 : 0;
        $photoCount = $property->photos()->count();
        $total += 2; $score += min(1, $photoCount) + (min(5, $photoCount) >= 5 ? 1 : 0);
        $percent = $total > 0 ? (int) round(($score / $total) * 100) : 0;
        $property->update(['completeness_percent' => min(100, $percent)]);
    }
}
