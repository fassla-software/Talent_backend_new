<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EnvoySetting;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use App\Http\Requests\UserRequest;

class EnvoyController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        // Query Envoys
        $usersQuery = User::role('envoy')->with('envoySetting');

        if ($searchTerm) {
            $usersQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('phone', 'like', '%' . $searchTerm . '%');
            });
        }

        $users = $usersQuery->paginate(20);

        // Stats
        $totalEnvoys = User::role('envoy')->count();
        
        // Fetch Total Visits from API
        $totalVisits = 0;
        try {
            $response = Http::get('https://app.talentindustrial.com/plumber/inspection-visit/admin', [
                'limit' => 1 // We only need the count if available in metadata, or we might need a specific stats endpoint
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                // Assuming the API returns a total count in pagination or similar structure
                // If not available directly, we might need to rely on what's available or ask for an endpoint update.
                // For now, let's assume 'pagination.total' or similar if it exists, otherwise 0.
                $totalVisits = $data['pagination']['total'] ?? 0;
            }
        } catch (\Exception $e) {
            // Log error or ignore
        }

        return view('admin.envoy.index', compact('users', 'totalEnvoys', 'totalVisits'));
    }

    public function create()
    {
        return view('admin.envoy.create');
    }

    public function store(UserRequest $request)
    {
        $user = User::where('phone', $request->phone)->first();
        if ($user) {
            return back()->withError(__('Phone number already exists'));
        }

        $request['is_active'] = true;
        // Force role to envoy
        $request['role'] = 'envoy';
        
        $user = UserRepository::storeByRequest($request);
        $user->assignRole('envoy');

        EnvoySetting::create([
            'user_id' => $user->id,
            'weight' => $request->weight ?? 0,
            'target' => $request->target ?? 0,
            'salary' => $request->salary ?? 0,
            'incentives' => $request->incentives ?? 0,
            'region' => $request->region,
        ]);

        WalletRepository::storeByRequest($user);

        return to_route('admin.envoy.index')->withSuccess(__('Created successfully'));
    }

    public function show(User $user, Request $request)
    {
        if (!$user->hasRole('envoy')) {
            abort(404);
        }

        $date = $request->get('date', now()->toDateString());
        $period = $request->get('period', 'week');
        $timingData = null;
        $salesStats = null;

        try {
            // Fetch Visit Timing
            $timingResponse = Http::post('https://app.talentindustrial.com/plumber/inspection-visit/timing', [
                'envoy_id' => $user->id,
                'date' => $date
            ]);

            if ($timingResponse->successful()) {
                $timingData = $timingResponse->json()['data'];
            }

            // Fetch Sales Stats
            $salesResponse = Http::post('https://app.talentindustrial.com/plumber/envoy/admin/stats', [
                'envoy_id' => $user->id,
                'period' => $period,
                'date' => $date
            ]);

            if ($salesResponse->successful()) {
                $salesStats = $salesResponse->json()['data'];
            }

            // Fetch Visits List
            $visitsResponse = Http::get('https://app.talentindustrial.com/plumber/inspection-visit/admin', [
                'inspector_id' => $user->id,
                'limit' => 50 // Fetch last 50 visits
            ]);

            if ($visitsResponse->successful()) {
                $visitsData = $visitsResponse->json();
                $visits = $visitsData['visits'] ?? [];
            } else {
                $visits = [];
            }

        } catch (\Exception $e) {
            logger()->error('Failed to fetch envoy data: ' . $e->getMessage());
            $visits = [];
        }

        return view('admin.envoy.show', compact('user', 'timingData', 'salesStats', 'visits'));
    }

    public function edit(User $user)
    {
        if (!$user->hasRole('envoy')) {
            abort(404);
        }
        return view('admin.envoy.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!$user->hasRole('envoy')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            // Add other validations as needed
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);

        EnvoySetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'weight' => $request->weight ?? 0,
                'target' => $request->target ?? 0,
                'salary' => $request->salary ?? 0,
                'incentives' => $request->incentives ?? 0,
                'region' => $request->region,
            ]
        );

        return to_route('admin.envoy.index')->withSuccess(__('Updated successfully'));
    }

    public function destroy(User $user)
    {
        if (!$user->hasRole('envoy')) {
            abort(404);
        }

        $user->syncRoles([]);
        $user->syncPermissions([]);

        $media = $user->media;
        if ($media && Storage::exists($media->src)) {
            Storage::delete($media->src);
        }

        $user->wallet()?->delete();
        $user->envoySetting()?->delete();
        $user->forceDelete();

        if ($media) {
            $media->delete();
        }

        return back()->withSuccess(__('Deleted successfully'));
    }
}
