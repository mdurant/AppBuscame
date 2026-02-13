<?php

namespace App\Http\Controllers\Api;

use App\Enums\PropertyStatus;
use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Property::query()
            ->with(['address', 'photos', 'source'])
            ->where('status', PropertyStatus::Published);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('source')) {
            $query->whereHas('source', fn ($q) => $q->where('slug', $request->source));
        }

        $perPage = min($request->integer('per_page', 15), 50);
        $items = $query->latest('published_at')->paginate($perPage);

        return response()->json($items);
    }

    public function show(Property $property): JsonResponse
    {
        if ($property->status !== PropertyStatus::Published) {
            abort(404);
        }
        $property->load(['address', 'photos', 'availabilityRanges', 'score']);

        return response()->json($property);
    }
}
