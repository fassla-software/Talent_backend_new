<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $levels = \App\Models\Level::all();
        return view('admin.levels.index', compact('levels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.levels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'level' => 'required|integer|unique:levels,level',
            'min_sales' => 'required|integer|min:0',
            'max_sales' => 'required|integer|min:0|gt:min_sales',
            'points' => 'integer|min:0',
        ]);

        \App\Models\Level::create([
            'level' => $request->level,
            'min_sales' => $request->min_sales,
            'max_sales' => $request->max_sales,
            'points' => $request->points ?? 0,
        ]);

        return redirect()->route('level.index')->with('success', 'Level created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
        $level = \App\Models\Level::findOrFail($id);
        return view('admin.levels.show', compact('level'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $level = \App\Models\Level::findOrFail($id);
        return view('admin.levels.edit', compact('level'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'level' => 'required|integer|unique:levels,level,' . $id,
            'min_sales' => 'required|integer|min:0',
            'max_sales' => 'required|integer|min:0|gt:min_sales',
            'points' => 'integer|min:0',
        ]);

        $level = \App\Models\Level::findOrFail($id);
        $level->update([
            'level' => $request->level,
            'min_sales' => $request->min_sales,
            'max_sales' => $request->max_sales,
            'points' => $request->points ?? 0,
        ]);
    
         return redirect()->route('level.index')->with('success', 'Level updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $level = \App\Models\Level::findOrFail($id);
        $level->delete();
        return redirect()->route('level.index')->with('success', 'Level deleted successfully.');
    }
}
