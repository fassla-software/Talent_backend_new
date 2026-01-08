<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TicketController extends Controller
{
    private $apiBaseUrl = 'https://app.talentindustrial.com/plumber/ticket';

    public function index()
    {
        try {
            $response = Http::get($this->apiBaseUrl . '/all');
            $tickets = $response->successful() ? $response->json() : [];

            return view('admin.tickets.index', compact('tickets'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch tickets: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'nullable|in:OPEN,IN_PROGRESS,CLOSED',
            'priority' => 'nullable|in:HIGH,AVERAGE,LOW',
            'due_date' => 'nullable|date',
            'note' => 'nullable|string',
            'close_reason' => 'nullable|string',
        ]);

        try {
            $response = Http::put($this->apiBaseUrl . '/' . $id, $request->all());

            if ($response->successful()) {
                return back()->with('success', 'Ticket updated successfully');
            }

            $error = $response->json()['message'] ?? 'Failed to update ticket';
            return back()->with('error', $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update ticket: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $response = Http::delete($this->apiBaseUrl . '/' . $id);

            if ($response->successful()) {
                return back()->with('success', 'Ticket deleted successfully');
            }

            $error = $response->json()['message'] ?? 'Failed to delete ticket';
            return back()->with('error', $error);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete ticket: ' . $e->getMessage());
        }
    }
}