<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InspectionVisitController extends Controller
{
    private $apiBaseUrl = 'https://app.talentindustrial.com/plumber/inspection-visit';

    public function index(Request $request)
    {
        try {
            
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);
            
            
            $response = Http::get($this->apiBaseUrl . '/admin', [
                'page' => $page,
                'limit' => $limit
            ]);

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