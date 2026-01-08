<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plumber; // Assuming plumbers are stored in the plumbers table
use App\Services\FirebaseService;
use App\Models\NotificationUnique;

class PlumberNotificationController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    // Show the form to send notifications to multiple plumbers
  public function create(Request $request)
{
    $city = $request->input('city');
    $area = $request->input('area');
    $minFixedPoints = $request->input('min_fixed_points');
    $maxFixedPoints = $request->input('max_fixed_points');

    $cities = Plumber::distinct()->pluck('city');
    $areas = Plumber::distinct()->pluck('area');

    $query = Plumber::with('user');

    if ($city) {
        $query->where('city', $city);
    }
    if ($area) {
        $query->where('area', $area);
    }
    if ($minFixedPoints !== null) {
        $query->where('fixed_points', '>=', $minFixedPoints);
    }
    if ($maxFixedPoints !== null) {
        $query->where('fixed_points', '<=', $maxFixedPoints);
    }

    $plumbers = $query->paginate(20); // Display 10 plumbers per page

    return view('admin.plumber_send_notification', compact('plumbers', 'cities', 'areas', 'city', 'area', 'minFixedPoints', 'maxFixedPoints'));
}




    public function sendNotificationMultiple(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        if ($request->has('send_to_all')) {
            $query = Plumber::with('user');

            if ($request->city) {
                $query->where('city', $request->city);
            }
            if ($request->area) {
                $query->where('area', $request->area);
            }
            if ($request->min_fixed_points !== null) {
                $query->where('fixed_points', '>=', $request->min_fixed_points);
            }
            if ($request->max_fixed_points !== null) {
                $query->where('fixed_points', '<=', $request->max_fixed_points);
            }

            $plumberUserIds = $query->pluck('user_id')->toArray();
        } else {
            $request->validate([
                'plumbers' => 'required|array|min:1',
            ]);
            $plumberUserIds = $request->plumbers;
        }

        // Fetch plumbers with users and their device tokens
        $filteredPlumbers = Plumber::whereIn('user_id', $plumberUserIds)
            ->with('user')
            ->get()
            ->filter(function ($plumber) {
                return $plumber->user && !empty($plumber->user->device_token);
            });

        $totalMatched = count($plumberUserIds);
        $withTokens = $filteredPlumbers->count();
        $missingTokens = $totalMatched - $withTokens;

        if ($filteredPlumbers->isEmpty()) {
            return redirect()->back()->with('error', "No valid plumbers with device tokens found. (Matched: $totalMatched, Missing Tokens: $missingTokens)");
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($filteredPlumbers as $plumber) {
            $deviceToken = $plumber->user->device_token;

            try {
                // Send notification
                $this->firebase->sendNotification($deviceToken, $request->title, $request->body);
                $successCount++;

                // Save notification to database
                NotificationUnique::create([
                    'user_id' => $plumber->user_id,
                    'title' => $request->title,
                    'body' => $request->body,
                    'device_token' => $deviceToken,
                    'type' => 'multiple',
                ]);
            } catch (\Exception $e) {
                $errorCount++;
            }
        }

        session()->flash('success', "Notification send to $totalMatched plumber success $successCount");
        if ($missingTokens > 0 || $errorCount > 0) {
            $totalFail = $missingTokens + $errorCount;
            session()->flash('error', "$totalFail plumbers had missing device token or failed to send.");
        }

        return redirect()->back();
    }




}
