<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Trader;
use App\Models\Plumber;

class InspectionVisitController extends Controller
{
    private $apiBaseUrl = 'https://app.talentindustrial.com/plumber/inspection-visit';

    public function index(Request $request)
    {
        try {
            
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);
            
            
            $params = [
                'page' => $page,
                'limit' => $limit
            ];

            if ($request->has('trader_id')) {
                $params['trader_id'] = $request->get('trader_id');
            }

            if ($request->has('plumber_id')) {
                $params['plumber_id'] = $request->get('plumber_id');
            }

            if ($request->has('status')) {
                $params['status'] = $request->get('status');
            }

            $response = Http::get($this->apiBaseUrl . '/admin', $params);

            if ($response->successful()) {
                $data = $response->json();
                return view('admin.inspection-visits.index', [
                    'visits' => $data['visits'] ?? [],
                    'pagination' => $data['pagination'] ?? []
                ]);
            }

            return back()->with('error', 'Failed to fetch inspection visits: ' . $response->body());
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $envoys = User::role('envoy')->get();
        $traders = Trader::with('user')->get();
        $plumbers = Plumber::with('user')->get();

        return view('admin.inspection-visits.create', compact('envoys', 'traders', 'plumbers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inspector_id' => 'required|exists:users,id',
            'client_type' => 'required|in:trader,plumber',
            'trader_id' => 'required_if:client_type,trader|nullable|exists:traders,id',
            'plumber_id' => 'required_if:client_type,plumber|nullable|exists:plumbers,id',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string'
        ]);

        try {
            $data = [
                'inspector_id' => $request->inspector_id,
                'scheduled_at' => \Carbon\Carbon::parse($request->scheduled_at)->toIso8601String(),
                'notes' => $request->notes
            ];

            if ($request->client_type == 'trader') {
                $data['trader_id'] = (int)$request->trader_id;
            } else {
                $data['plumber_id'] = (int)$request->plumber_id;
            }

            $response = Http::post($this->apiBaseUrl . '/admin/schedule', $data);

            if ($response->successful()) {
                return redirect()->route('admin.inspectionVisit.index')->with('success', 'Visit scheduled successfully');
            }

            $error = $response->json()['message'] ?? 'Failed to schedule visit';
            return back()->withInput()->with('error', $error);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $response = Http::get($this->apiBaseUrl . '/admin/' . $id);

            if ($response->successful()) {
                $data = $response->json();
                return view('admin.inspection-visits.show', [
                    'visit' => $data['data']
                ]);
            }

            return back()->with('error', 'Visit not found');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:PENDING,APPROVED,REJECTED'
        ]);

        try {
            $response = Http::put($this->apiBaseUrl . '/admin/' . $id . '/status', [
                'status' => $request->status
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Visit status updated successfully');
            }

            $error = $response->json()['message'] ?? 'Failed to update status';
            return back()->with('error', $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}