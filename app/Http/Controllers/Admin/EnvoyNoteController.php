<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnvoyNote;
use App\Models\User;
use Illuminate\Http\Request;

class EnvoyNoteController extends Controller
{
    /**
     * Display a listing of the notes.
     */
    public function index(Request $request)
    {
        $clientId = $request->input('client_id');
        $notes = EnvoyNote::with(['envoy', 'client'])
            ->when($clientId, function ($query) use ($clientId) {
                return $query->where('client_id', $clientId);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Clients can be Plumbers or Traders
        $clients = User::role(['plumber', 'trader'])->get();

        return view('admin.envoy_notes.index', compact('notes', 'clients'));
    }

    /**
     * Show the form for creating a new note.
     */
    public function create()
    {
        $clients = User::role(['plumber', 'trader'])->get();

        return view('admin.envoy_notes.create', compact('clients'));
    }

    /**
     * Store a newly created note in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'envoy_id' => 'nullable|exists:users,id',
            'client_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        EnvoyNote::create($validated);

        return redirect()->route('admin.envoy-notes.index')->withSuccess(__('Note created successfully'));
    }

    /**
     * Show the form for editing the specified note.
     */
    public function edit(EnvoyNote $envoy_note)
    {
        $clients = User::role(['plumber', 'trader'])->get();

        return view('admin.envoy_notes.edit', compact('envoy_note', 'clients'));
    }

    /**
     * Update the specified note in storage.
     */
    public function update(Request $request, EnvoyNote $envoy_note)
    {
        $validated = $request->validate([
            'envoy_id' => 'nullable|exists:users,id',
            'client_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $envoy_note->update($validated);

        return redirect()->route('admin.envoy-notes.index')->withSuccess(__('Note updated successfully'));
    }

    /**
     * Remove the specified note from storage.
     */
    public function destroy(EnvoyNote $envoy_note)
    {
        $envoy_note->delete();
        return back()->withSuccess(__('Note deleted successfully'));
    }
}
