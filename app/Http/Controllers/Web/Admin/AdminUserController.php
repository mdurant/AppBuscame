<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $consultationsSub = '(SELECT COALESCE(SUM(pv.id), 0) FROM property_views pv INNER JOIN properties p ON p.id = pv.property_id WHERE p.user_id = users.id)';

        $query = User::query()
            ->with('profile')
            ->selectRaw("users.*, {$consultationsSub} as total_consultations");

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhereHas('profile', fn ($q2) => $q2->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        $sortCol = $request->get('sort', 'created_at');
        $sortDir = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowed = ['name', 'email', 'created_at', 'region', 'city', 'total_consultations'];
        if (in_array($sortCol, $allowed, true)) {
            if ($sortCol === 'region' || $sortCol === 'city') {
                $query->join('profiles', 'profiles.user_id', '=', 'users.id')
                    ->orderBy("profiles.{$sortCol}", $sortDir)
                    ->selectRaw("users.*, {$consultationsSub} as total_consultations");
            } elseif ($sortCol === 'total_consultations') {
                $query->orderByRaw("{$consultationsSub} {$sortDir}");
            } else {
                $query->orderBy("users.{$sortCol}", $sortDir);
            }
        } else {
            $query->orderBy('users.created_at', 'desc');
        }

        $total = User::count();
        $users = $query->paginate(10)->withQueryString();

        $rankBase = ($users->currentPage() - 1) * $users->perPage();
        $users->getCollection()->transform(function ($user, $index) use ($rankBase) {
            $user->ranking = $rankBase + $index + 1;
            return $user;
        });

        return view('dashboard.admin.users.index', [
            'users' => $users,
            'total' => $total,
        ]);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        $user->load(['profile', 'properties' => fn ($q) => $q->withCount('propertyViews')->with('address')]);
        $totalConsultations = (int) $user->properties->sum('property_views_count');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at->format('d/m/Y H:i'),
            'profile' => $user->profile ? [
                'first_name' => $user->profile->first_name,
                'last_name' => $user->profile->last_name,
                'phone' => $user->profile->phone,
                'region' => $user->profile->region,
                'city' => $user->profile->city,
            ] : null,
            'published_properties_count' => $user->properties->count(),
            'total_consultations' => $totalConsultations,
            'verification_status' => $user->verification_status?->value ?? null,
            'properties' => $user->properties->map(fn ($p) => [
                'id' => $p->id,
                'type' => $p->type,
                'status' => $p->status?->value ?? $p->status,
                'published_at' => $p->published_at?->format('d/m/Y'),
                'address' => $p->address ? $p->address->city . ', ' . $p->address->region : null,
                'views_count' => $p->property_views_count ?? 0,
            ]),
        ]);
    }
}
