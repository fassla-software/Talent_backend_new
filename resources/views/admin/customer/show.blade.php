@extends('layouts.app')

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            <i class="fa-solid fa-user"></i>{{__('Customer Details')}}
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ $user->thumbnail ?? asset('defualt/defualt.jpg') }}" 
                         alt="Profile" 
                         class="rounded-circle mb-3" 
                         width="150" height="150" style="object-fit: cover;">
                    <h4>{{ $user->name }} {{ $user->last_name }}</h4>
                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                        {{ $user->is_active ? __('Active') : __('Inactive') }}
                    </span>
                    <p class="text-muted mt-2">{{ __('Customer') }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{__('Personal Information')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">{{ __('Phone') }}:</div>
                        <div class="col-md-8">{{ $user->phone }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">{{ __('Email') }}:</div>
                        <div class="col-md-8">{{ $user->email ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">{{ __('Gender') }}:</div>
                        <div class="col-md-8">{{ ucfirst($user->gender ?? 'N/A') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">{{ __('Date of Birth') }}:</div>
                        <div class="col-md-8">{{ $user->date_of_birth ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">{{ __('Joined At') }}:</div>
                        <div class="col-md-8">{{ $user->created_at->format('d M, Y') }}</div>
                    </div>
                </div>
            </div>

            @if($customer && $customer->orders->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">{{__('Recent Orders')}}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Order Code') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->orders()->latest()->take(5)->get() as $order)
                                <tr>
                                    <td>{{ $order->prefix ?? '' }}{{ $order->order_code }}</td>
                                    <td>{{ $order->created_at->format('d M, Y') }}</td>
                                    <td>{{ showCurrency($order->payable_amount) }}</td>
                                    <td>{{ $order->order_status }}</td>
                                    <td>
                                        <a href="{{ route('admin.order.show', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
