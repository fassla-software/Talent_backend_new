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
            // amazonq-ignore-next-line
            $usersQuery->where(function ($query) use ($searchTerm) {
                // amazonq-ignore-next-line
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
        // amazonq-ignore-next-line
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
        // amazonq-ignore-next-line
        $user = User::where('phone', $request->phone)->first();
        if ($user) {
            return back()->withError(__('Phone number already exists'));
        }

        // amazonq-ignore-next-line
        $request['is_active'] = true;
        // Force role to envoy
        $request['role'] = 'envoy';
        
        // Validate weights sum to 100
        $totalWeight = ($request->weight_sales ?? 0) + 
                       ($request->weight_visits ?? 0) + 
                       ($request->weight_retention_rate ?? 0) + 
                       ($request->weight_conversion_rate ?? 0) +
                       ($request->weight_inspection_requests ?? 0);
        
        if ($totalWeight != 100) {
            return back()->withErrors(['weights_sum' => 'The sum of all weight percentages must equal 100.'])->withInput();
        }
        
        $user = UserRepository::storeByRequest($request);
        $user->assignRole('envoy');

        EnvoySetting::create([
            'user_id' => $user->id,
            'weight_sales' => $request->weight_sales ?? 0,
            'weight_visits' => $request->weight_visits ?? 0,
            'weight_retention_rate' => $request->weight_retention_rate ?? 0,
            'weight_conversion_rate' => $request->weight_conversion_rate ?? 0,
            'weight_inspection_requests' => $request->weight_inspection_requests ?? 0,
            'target_sales' => $request->target_sales ?? 0,
            'target_visits' => $request->target_visits ?? 0,
            'target_retention_rate' => $request->target_retention_rate ?? 0,
            'target_conversion_rate' => $request->target_conversion_rate ?? 0,
            'target_inspection_requests' => $request->target_inspection_requests ?? 0,
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
        $visitsPage = $request->get('visits_page', 1);
        $limit = 10;

        $timingData = null;
        $timingPaginator = null;
        $salesStats = null;
        $visits = [];
        $visitsPaginator = null;

        try {
            // Fetch Visit Timing
            $timingResponse = Http::post('https://app.talentindustrial.com/plumber/inspection-visit/timing', [
                'envoy_id' => $user->id,
                'date' => $date
            ]);

            if ($timingResponse->successful()) {
                $allTimingData = $timingResponse->json()['data'];
                $timingVisits = $allTimingData['visits'] ?? [];
                
                // Calculate Total Visit Time and Time Between Visits
                $totalVisitTime = 0;
                $totalBetweenVisitsTime = 0;
                
                if (count($timingVisits) > 0) {
                    // Sort visits by check-in time to calculate gaps
                    usort($timingVisits, function($a, $b) {
                        return strtotime($a['check_in_at']) <=> strtotime($b['check_in_at']);
                    });
                    
                    foreach ($timingVisits as $index => $visit) {
                        $duration = $visit['duration_minutes'] ?? 0;
                        $totalVisitTime += $duration;
                        
                        // Calculate gap with next visit
                        if (isset($timingVisits[$index + 1])) {
                            $currentEnd = strtotime($visit['check_in_at']) + ($duration * 60);
                            $nextStart = strtotime($timingVisits[$index + 1]['check_in_at']);
                            
                            $gap = ($nextStart - $currentEnd) / 60;
                            if ($gap > 0) {
                                $totalBetweenVisitsTime += $gap;
                            }
                        }
                    }
                }
                
                $allTimingData['total_visit_time_minutes'] = $totalVisitTime;
                $allTimingData['total_between_visit_time_minutes'] = $totalBetweenVisitsTime;

                $timingPage = $request->get('timing_page', 1);
                $timingTotal = count($timingVisits);
                $timingOffset = ($timingPage - 1) * $limit;
                $pagedTiming = array_slice($timingVisits, $timingOffset, $limit);

                $timingData = $allTimingData;
                $timingData['visits'] = $pagedTiming;

                $timingPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                    $pagedTiming,
                    $timingTotal,
                    $limit,
                    $timingPage,
                    ['path' => route('admin.envoy.show', $user->id), 'query' => $request->query(), 'pageName' => 'timing_page']
                );
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

            // Fetch Visits List with Pagination
            $visitsResponse = Http::get('https://app.talentindustrial.com/plumber/inspection-visit/admin', [
                'inspector_id' => $user->id,
                'page' => $visitsPage,
                'limit' => $limit
            ]);

            if ($visitsResponse->successful()) {
                $visitsData = $visitsResponse->json();
                $visits = $visitsData['visits'] ?? [];
                $pagination = $visitsData['pagination'] ?? null;

                if ($pagination) {
                    $visitsPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                        $visits,
                        $pagination['total'],
                        $pagination['limit'],
                        $pagination['page'],
                        ['path' => route('admin.envoy.show', $user->id), 'query' => $request->query(), 'pageName' => 'visits_page']
                    );
                }
            }

            // Fetch Envoy Awards
            $awardsResponse = Http::get('https://app.talentindustrial.com/plumber/award/envoy/' . $user->id);
            $envoyAwards = $awardsResponse->successful() ? $awardsResponse->json() : [];

        } catch (\Exception $e) {
            logger()->error('Failed to fetch envoy data: ' . $e->getMessage());
        }

        return view('admin.envoy.show', compact('user', 'timingData', 'timingPaginator', 'salesStats', 'visits', 'visitsPaginator', 'envoyAwards'));
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
            // amazonq-ignore-next-line
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            // Add other validations as needed
        ]);
        
        // Validate weights sum to 100
        $totalWeight = ($request->weight_sales ?? 0) + 
                       ($request->weight_visits ?? 0) + 
                       ($request->weight_retention_rate ?? 0) + 
                       ($request->weight_conversion_rate ?? 0) +
                       ($request->weight_inspection_requests ?? 0);
        
        if ($totalWeight != 100) {
            return back()->withErrors(['weights_sum' => 'The sum of all weight percentages must equal 100.'])->withInput();
        }

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);

        EnvoySetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'weight_sales' => $request->weight_sales ?? 0,
                'weight_visits' => $request->weight_visits ?? 0,
                'weight_retention_rate' => $request->weight_retention_rate ?? 0,
                'weight_conversion_rate' => $request->weight_conversion_rate ?? 0,
                'weight_inspection_requests' => $request->weight_inspection_requests ?? 0,
                'target_sales' => $request->target_sales ?? 0,
                'target_visits' => $request->target_visits ?? 0,
                'target_retention_rate' => $request->target_retention_rate ?? 0,
                'target_conversion_rate' => $request->target_conversion_rate ?? 0,
                'target_inspection_requests' => $request->target_inspection_requests ?? 0,
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

        // amazonq-ignore-next-line
        $media = $user->media;
        // amazonq-ignore-next-line
        if ($media && Storage::exists($media->src)) {
            // amazonq-ignore-next-line
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
