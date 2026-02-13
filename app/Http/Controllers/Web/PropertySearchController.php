<?php

namespace App\Http\Controllers\Web;

use App\Enums\PropertyStatus;
use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use App\Models\Property\Source;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertySearchController extends Controller
{
    public function index(Request $request): View
    {
        $sources = Source::where('is_active', true)->orderBy('name')->get();

        $query = Property::query()
            ->with(['address', 'photos', 'source'])
            ->where('status', PropertyStatus::Published);

        if ($request->filled('category')) {
            $query->where('type', $request->category);
        }
        if ($request->filled('location')) {
            $search = $request->location;
            $query->whereHas('address', function ($q) use ($search) {
                $q->where('city', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%")
                    ->orWhere('address_line', 'like', "%{$search}%");
            });
        }
        if ($request->filled('source')) {
            $query->whereHas('source', fn ($q) => $q->where('slug', $request->source));
        }
        if ($request->filled('price_min') && is_numeric($request->price_min)) {
            $query->where('cost_amount', '>=', $request->price_min);
        }
        if ($request->filled('price_max') && is_numeric($request->price_max)) {
            $query->where('cost_amount', '<=', $request->price_max);
        }
        if ($request->filled('bedrooms') && is_numeric($request->bedrooms)) {
            // Si más adelante tienes campo bedrooms en properties, filtrar aquí
        }
        if ($request->filled('bathrooms') && is_numeric($request->bathrooms)) {
            // Idem
        }

        $properties = $query->latest('published_at')->paginate(12)->withQueryString();

        return view('landing', [
            'properties' => $properties,
            'sources' => $sources,
            'filters' => $request->only(['category', 'location', 'source', 'price_min', 'price_max']),
        ]);
    }
}
