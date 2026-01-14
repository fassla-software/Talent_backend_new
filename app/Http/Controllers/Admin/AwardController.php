<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AwardController extends Controller
{
    private $apiBaseUrl = 'https://app.talentindustrial.com/plumber/award';

    // Awards CRUD
    public function index()
    {
        try {
            $response = Http::get($this->apiBaseUrl);
            $awards = $response->successful() ? $response->json() : [];

            return view('admin.awards.index', compact('awards'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch awards: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        try {
            $response = Http::post($this->apiBaseUrl, $request->all());

            if ($response->successful()) {
                return back()->with('success', 'Award created successfully');
            }

            $error = $response->json()['message'] ?? 'Failed to create award';
            return back()->with('error', $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create award: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        try {
            $response = Http::put($this->apiBaseUrl . '/' . $id, $request->all());

            if ($response->successful()) {
                return back()->with('success', 'Award updated successfully');
            }

            $error = $response->json()['message'] ?? 'Failed to update award';
            return back()->with('error', $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update award: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $response = Http::delete($this->apiBaseUrl . '/' . $id);

            if ($response->successful()) {
                return back()->with('success', 'Award deleted successfully');
            }

            $error = $response->json()['message'] ?? 'Failed to delete award';
            return back()->with('error', $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete award: ' . $e->getMessage());
        }
    }

    // Award Assignments
    public function envoyAwards(Request $request)
    {
        try {
            $response = Http::get($this->apiBaseUrl . '/envoy-awards', [
                'page' => $request->query('page', 1),
                'limit' => $request->query('limit', 10),
                'search' => $request->query('search'),
            ]);
            $data = $response->successful() ? $response->json() : ['data' => [], 'total' => 0];

            $awardsResponse = Http::get($this->apiBaseUrl);
            $awards = $awardsResponse->successful() ? $awardsResponse->json() : [];

            return view('admin.awards.envoy_awards', compact('data', 'awards'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch envoy awards: ' . $e->getMessage());
        }
    }

    public function assignForm()
    {
        try {
            $awardsResponse = Http::get($this->apiBaseUrl);
            $awards = $awardsResponse->successful() ? $awardsResponse->json() : [];

            $envoysResponse = Http::get($this->apiBaseUrl . '/envoys/list');
            $envoys = $envoysResponse->successful() ? $envoysResponse->json() : [];

            return view('admin.awards.assign', compact('awards', 'envoys'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load assignment form: ' . $e->getMessage());
        }
    }

    public function assign(Request $request)
    {
        $request->validate([
            'envoy_id' => 'required|integer',
            'award_id' => 'required|integer',
            'reason' => 'nullable|string',
        ]);

        try {
            $response = Http::post($this->apiBaseUrl . '/assign', $request->all());

            if ($response->successful()) {
                return redirect()->route('admin.award.envoy-awards')->with('success', 'Award assigned successfully');
            }

            $error = $response->json()['message'] ?? 'Failed to assign award';
            return back()->with('error', $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign award: ' . $e->getMessage());
        }
    }

    public function updateAssignment(Request $request, $id)
    {
        $request->validate([
            'envoy_id' => 'required|integer',
            'award_id' => 'required|integer',
            'reason' => 'nullable|string',
        ]);

        try {
            $response = Http::put($this->apiBaseUrl . '/envoy-awards/' . $id, $request->all());

            if ($response->successful()) {
                return back()->with('success', 'Assignment updated successfully');
            }

            $error = $response->json()['message'] ?? 'Failed to update assignment';
            return back()->with('error', $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update assignment: ' . $e->getMessage());
        }
    }

    public function destroyAssignment($id)
    {
        try {
            $response = Http::delete($this->apiBaseUrl . '/envoy-awards/' . $id);

            if ($response->successful()) {
                return back()->with('success', 'Assignment deleted successfully');
            }

            $error = $response->json()['message'] ?? 'Failed to delete assignment';
            return back()->with('error', $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete assignment: ' . $e->getMessage());
        }
    }
}
