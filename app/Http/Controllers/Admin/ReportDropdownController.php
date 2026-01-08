<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReportDropdownController extends Controller
{
    private $apiBaseUrl = 'https://app.talentindustrial.com/plumber/report-dropdowns';

    public function index()
    {
        try {
            // Get all dropdown types
            $typesResponse = Http::get($this->apiBaseUrl . '/types');
            $types = $typesResponse->successful() ? $typesResponse->json()['data'] : [];

            // Get all options for each type
            $dropdowns = [];
            foreach ($types as $type) {
                $optionsResponse = Http::get($this->apiBaseUrl . '/' . $type);
                if ($optionsResponse->successful()) {
                    $dropdowns[$type] = $optionsResponse->json()['data'];
                }
            }

            return view('admin.report-dropdowns.index', compact('dropdowns', 'types'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch dropdown options: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'dropdown_type' => 'required|string',
            'key' => 'required|string',
            'value_en' => 'required|string',
            'value_ar' => 'nullable|string',
        ]);

        try {
            $response = Http::post($this->apiBaseUrl, $request->all());

            if ($response->successful()) {
                return back()->with('success', 'Option added successfully');
            }

            $error = $response->json()['message'] ?? 'Failed to add option';
            return back()->with('error', $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add option: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'key' => 'required|string',
            'value_en' => 'required|string',
            'value_ar' => 'nullable|string',
        ]);

        try {
            $response = Http::put($this->apiBaseUrl . '/' . $id, $request->all());

            if ($response->successful()) {
                return back()->with('success', 'Option updated successfully');
            }

            $error = $response->json()['message'] ?? 'Failed to update option';
            return back()->with('error', $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update option: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $response = Http::delete($this->apiBaseUrl . '/' . $id);

            if ($response->successful()) {
                return back()->with('success', 'Option deleted successfully');
            }

            $error = $response->json()['message'] ?? 'Failed to delete option';
            return back()->with('error', $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete option: ' . $e->getMessage());
        }
    }
}