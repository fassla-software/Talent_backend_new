<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\Plumber;
use App\Models\Trader;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UnifiedUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');
        $status_filter = $request->get('status');
        $city_filter = $request->get('city');

        // Fetch unique cities
        $cities = collect()
            ->concat(Trader::select('city')->distinct()->pluck('city'))
            ->concat(Plumber::select('city')->distinct()->pluck('city'))
            ->concat(Distributor::select('city')->distinct()->pluck('city'))
            ->unique()
            ->filter()
            ->sort()
            ->values();

        // Helper to calculate status counts
        $getStatusCounts = function($roleFilter = null, $cityFilter = null) {
            $statuses = ['PENDING', 'ACTIVE', 'INACTIVE', 'DORMANT'];
            $counts = array_fill_keys(array_map('strtolower', $statuses), 0);
            
            if (!$roleFilter || $roleFilter === 'trader') {
                foreach ($statuses as $status) {
                    $counts[strtolower($status)] += Trader::where('status', $status)
                        ->when($cityFilter, fn($q) => $q->where('city', $cityFilter))
                        ->count();
                }
            }
            
            if (!$roleFilter || $roleFilter === 'plumber') {
                foreach ($statuses as $status) {
                    $counts[strtolower($status)] += Plumber::where('status', $status)
                        ->when($cityFilter, fn($q) => $q->where('city', $cityFilter))
                        ->count();
                }
            }
            
            return $counts;
        };

        $statusCounts = $getStatusCounts($role, $city_filter);

        // Stats
        $stats = [
            'traders' => Trader::when($city_filter, fn($q) => $q->where('city', $city_filter))
                ->when($role && $role !== 'trader', fn($q) => $q->whereRaw('1=0'))
                ->count(),
            'plumbers' => Plumber::when($city_filter, fn($q) => $q->where('city', $city_filter))
                ->when($role && $role !== 'plumber', fn($q) => $q->whereRaw('1=0'))
                ->count(),
            'distributors' => Distributor::when($city_filter, fn($q) => $q->where('city', $city_filter))
                ->when($role && $role !== 'distributor', fn($q) => $q->whereRaw('1=0'))
                ->count(),
            'envoys' => User::role('envoy')
                ->when($city_filter, function($q) use ($city_filter) {
                    $q->whereHas('envoySetting', fn($sq) => $sq->where('region', $city_filter));
                })
                ->when($role && $role !== 'envoy', fn($q) => $q->whereRaw('1=0'))
                ->count(),
            'status_counts' => $statusCounts,
        ];

        $users = collect();

        // Fetch and unify Traders
        if (!$role || $role === 'trader') {
            $traders = Trader::with('user')
                ->when($search, function ($query) use ($search) {
                    $query->whereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->when($status_filter, function ($query) use ($status_filter) {
                    $query->where('status', $status_filter);
                })
                ->when($city_filter, function ($query) use ($city_filter) {
                    $query->where('city', $city_filter);
                })
                ->get()
                ->map(function ($trader) {
                    return [
                        'id' => $trader->id,
                        'name' => $trader->user->name ?? 'N/A',
                        'phone' => $trader->user->phone ?? 'N/A',
                        'role' => 'trader',
                        'status' => $trader->status ?? 'N/A',
                        'profile_route' => route('admin.traders.show', $trader->id),
                    ];
                });
            $users = $users->concat($traders);
        }

        // Fetch and unify Plumbers
        if (!$role || $role === 'plumber') {
            $plumbers = Plumber::with('user')
                ->when($search, function ($query) use ($search) {
                    $query->whereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->when($status_filter, function ($query) use ($status_filter) {
                    $query->where('status', $status_filter);
                })
                ->when($city_filter, function ($query) use ($city_filter) {
                    $query->where('city', $city_filter);
                })
                ->get()
                ->map(function ($plumber) {
                    return [
                        'id' => $plumber->id,
                        'name' => $plumber->user->name ?? 'N/A',
                        'phone' => $plumber->user->phone ?? 'N/A',
                        'role' => 'plumber',
                        'status' => $plumber->status ?? 'N/A',
                        'profile_route' => route('admin.plumberUsers.show', $plumber->id),
                    ];
                });
            $users = $users->concat($plumbers);
        }

        // Fetch and unify Distributors
        if (!$role || $role === 'distributor') {
            $distributorsArr = Distributor::query()
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                })
                ->when($status_filter, function ($query) use ($status_filter) {
                    // Distributors have 'state' instead of 'status', and it might not map exactly
                    // For now, we only filter if it matched exactly or just skip if no mapping
                    $query->where('state', $status_filter);
                })
                ->when($city_filter, function ($query) use ($city_filter) {
                    $query->where('city', $city_filter);
                })
                ->get()
                ->map(function ($distributor) {
                    return [
                        'id' => $distributor->id,
                        'name' => $distributor->name,
                        'phone' => $distributor->phone,
                        'role' => 'distributor',
                        'status' => $distributor->state ?? 'N/A',
                        'profile_route' => route('admin.distributor.show', $distributor->id),
                    ];
                });
            $users = $users->concat($distributorsArr);
        }

        // Fetch and unify Envoys
        if (!$role || $role === 'envoy') {
            $envoys = User::role('envoy')
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->when($status_filter, function ($query) use ($status_filter) {
                    $isActive = strtoupper($status_filter) === 'ACTIVE' ? 1 : (strtoupper($status_filter) === 'INACTIVE' ? 0 : null);
                    if ($isActive !== null) {
                        $query->where('is_active', $isActive);
                    } else {
                        // If status is PENDING or DORMANT, and envoy doesn't have those, return empty or handle
                        $query->whereRaw('1 = 0');
                    }
                })
                ->when($city_filter, function ($query) use ($city_filter) {
                    $query->whereHas('envoySetting', function ($q) use ($city_filter) {
                        $q->where('region', $city_filter);
                    });
                })
                ->get()
                ->map(function ($envoy) {
                    return [
                        'id' => $envoy->id,
                        'name' => $envoy->name,
                        'phone' => $envoy->phone,
                        'role' => 'envoy',
                        'status' => $envoy->is_active ? 'Active' : 'Inactive',
                        'profile_route' => route('admin.envoy.show', $envoy->id),
                    ];
                });
            $users = $users->concat($envoys);
        }

        // Pagination
        $perPage = 15;
        $page = $request->get('page', 1);
        $offset = ($page * $perPage) - $perPage;

        $pagedUsers = new LengthAwarePaginator(
            $users->slice($offset, $perPage)->values(),
            $users->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.unified-users.index', compact('pagedUsers', 'stats', 'search', 'role', 'status_filter', 'city_filter', 'cities'));
    }
}
