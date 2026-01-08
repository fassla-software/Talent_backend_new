<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\Plumber; 

class DistributorController extends Controller
{
    /**
     * Display a listing of the distributors.
     */
public function index(Request $request)
{
    $query = Distributor::query();

    // Combined search by phone, name, or address
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('phone', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%");
        });
    }

    // Filter by date range (created_at)
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('created_at', [
            $request->from_date . ' 00:00:00',
            $request->to_date . ' 23:59:59'
        ]);
    } elseif ($request->filled('from_date')) {
        $query->whereDate('created_at', '>=', $request->from_date);
    } elseif ($request->filled('to_date')) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    $distributors = $query->orderBy('id', 'desc')
                          ->paginate(15)
                          ->appends($request->query());

   $allCities = Plumber::pluck('city')->unique()->sort();  

    return view('admin.distributors.index', compact('distributors' , 'allCities'));
}


public function show($id)
{
    $distributor = Distributor::findOrFail($id);

    // Total coupons
    $totalCoupons = $distributor->coupons()->count();

    // Active coupons: status = active and not expired
    $activeCoupons = $distributor->coupons()
        ->where('status', 'active')
        ->count();

    // Used coupons
    $usedCoupons = $distributor->coupons()->where('status', 'used')->count();

    // Expired coupons
    $expiredCoupons = $distributor->coupons()
        ->where(function($q) {
            $q->where('status', 'expired');
        })
        ->count();

    return view('admin.distributors.show', compact(
        'distributor',
        'totalCoupons',
        'activeCoupons',
        'usedCoupons',
        'expiredCoupons'
    ));
}






    /**
     * Store a new distributor.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'state' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);

        $distributor = Distributor::create($data);

        return response()->json([
            'success' => true,
            'distributor' => $distributor,
        ], 201);
    }

public function edit($id)
{
    $distributor = Distributor::findOrFail($id);
    $allCities = Plumber::pluck('city')->unique()->sort();  
    return view('admin.distributors.edit', compact('distributor', 'allCities'));
}


    /**
     * Update the specified distributor.
     */


public function update(Request $request, $id)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'city' => 'nullable|string|max:255',
        'phone' => 'required|string|max:50',
        'state' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:255',
    ]);

    $distributor = Distributor::findOrFail($id);
    $distributor->update($data);

    // Redirect to distributor list with a success message
    return redirect()->route('admin.distributor.index')
                     ->with('success', 'Distributor updated successfully.');
}

    /**
     * Remove the specified distributor.
     */
public function destroy($id)
{
    $distributor = Distributor::findOrFail($id);
    $distributor->delete();

    return redirect()->route('admin.distributor.index')
                     ->with('success', 'Distributor deleted successfully.');
}

}
