<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\User;
use App\Repositories\CustomerRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
{
    $customers = User::role(Roles::CUSTOMER->value)
        ->orderByDesc('created_at')
        ->paginate(10);

    return view('admin.customer.index', compact('customers'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.customer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {
        $user = UserRepository::storeByRequest($request);
        $user->assignRole(Roles::CUSTOMER->value);

        CustomerRepository::storeByRequest($user);
        WalletRepository::storeByRequest($user);

        return to_route('admin.customer.index')->withSuccess(__('Customer created successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $customer = $user->customer;
        return view('admin.customer.show', compact('user', 'customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.customer.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, User $user)
    {
        if (app()->environment() == 'local' && $user->phone == '01700000000') {
            return back()->with('demoMode', 'You can not update the customer in demo mode');
        }

        UserRepository::updateByRequest($request, $user);

        return to_route('admin.customer.index')->withSuccess(__('Customer updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (app()->environment() == 'local' && $user->phone == '01700000000') {
            return back()->with('demoMode', 'You can not delete the customer in demo mode');
        }

        $user->delete();

        return back()->withSuccess(__('Customer deleted successfully'));
    }
}
