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

    public function show($id)
    {
        try {
            $response = Http::get($this->apiBaseUrl . '/' . $id);
            $ticket = $response->successful() ? $response->json() : null;

            if (!$ticket) {
                return back()->with('error', 'Ticket not found');
            }

            return view('admin.tickets.show', compact('ticket'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch ticket details: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'nullable|in:OPEN,IN_PROGRESS,CLOSED',
            'priority' => 'nullable|in:HIGH,AVERAGE,LOW',
            'due_date' => 'nullable|date_format:Y-m-d',
            'note' => 'nullable|string',
            'close_reason' => 'nullable|string',
        ]);

        try {
            // Only send fields that the API expects
            $data = array_filter([
                'status' => $request->input('status'),
                'priority' => $request->input('priority'),
                'due_date' => $request->input('due_date'),
                'note' => $request->input('note'),
                'close_reason' => $request->input('close_reason'),
            ], function($value) {
                return $value !== null && $value !== '';
            });
            
            // Convert due_date to ISO8601 format if provided
            if (!empty($data['due_date'])) {
                $data['due_date'] = date('c', strtotime($data['due_date'] . ' 00:00:00'));
            }
            
            $response = Http::put($this->apiBaseUrl . '/' . $id, $data);

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