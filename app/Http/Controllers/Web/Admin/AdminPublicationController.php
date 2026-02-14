<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use Illuminate\View\View;

class AdminPublicationController extends Controller
{
    public function index(): View
    {
        $properties = Property::query()
            ->with(['user.profile', 'address', 'photos'])
            ->withCount('propertyViews')
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate(15);

        return view('dashboard.admin.publications.index', [
            'properties' => $properties,
        ]);
    }

    public function show(Property $property): View
    {
        $property->load(['user.profile', 'address', 'photos', 'propertyViews' => fn ($q) => $q->with('user.profile')->latest('viewed_at')->limit(50)]);
        $property->loadCount('propertyViews');
        $threads = $property->messageThreads()->with(['participants.user.profile', 'messages.user.profile'])->withCount('messages')->get();

        return view('dashboard.admin.publications.show', [
            'property' => $property,
            'threads' => $threads,
        ]);
    }
}
