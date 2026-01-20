@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Trader Details</h3>
        <div>
            <a href="{{ route('admin.inspectionVisit.index', ['trader_id' => $trader->id]) }}" class="btn btn-primary me-2">
                <i class="bi bi-calendar-check"></i> View Visits
            </a>
            <a href="{{ route('admin.ticket.index', ['client_id' => $trader->user_id]) }}" class="btn btn-info me-2">
                <i class="bi bi-ticket-perforated"></i> View Tickets
            </a>
            <a href="{{ route('admin.inspectionRequest.index', ['requestor_id' => $trader->user_id]) }}" class="btn btn-success me-2">
                <i class="bi bi-clipboard-check"></i> View Inspection Requests
            </a>
            <a href="{{ route('admin.traders') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Coupons Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center bg-primary text-white p-3 shadow-sm">
                <h6>Total Used Coupons</h6>
                <p class="fs-4 fw-bold">{{ $totalCoupons }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center bg-success text-white p-3 shadow-sm">
                <h6>Total Sales Value</h6>
                <p class="fs-4 fw-bold">{{ number_format($totalSalesValue, 2) }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center bg-warning text-white p-3 shadow-sm">
                <h6>Current Level</h6>
                <p class="fs-4 fw-bold">{{ $currentLevel ? $currentLevel->level : 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Trader Info Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row mb-3">

                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">Name</h6>
                    <p class="fw-bold">{{ $trader->user->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">Phone</h6>
                    <p class="fw-bold">{{ $trader->user->phone ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">City</h6>
                    <p class="fw-bold">{{ $trader->city }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">Area</h6>
                    <p class="fw-bold">{{ $trader->area }}</p>
                </div>

                <div class="col-md-4 mb-3">
                    <h6 class="text-muted">Verified</h6>
                    <p class="fw-bold">
                        <span class="badge {{ $trader->is_verified ? 'bg-success' : 'bg-danger' }}">
                            {{ $trader->is_verified ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Created At</h6>
                    <p class="fw-bold text-success">{{ $trader->created_at?->format('Y-m-d H:i') }}</p>
                </div>
               
            </div>
        </div>
    </div>

    <!-- Coupons Table -->
    @if($totalCoupons)
    <div class="card shadow-sm border-0">
        <div class="card-body">
            @php
                $usedCouponsList = $trader->usedCoupons->where('status','used');
            @endphp
            @include('admin.traders.partials.coupon-table', ['coupons' => $usedCouponsList])
        </div>
    </div>
    @endif

</div>
@endsection
