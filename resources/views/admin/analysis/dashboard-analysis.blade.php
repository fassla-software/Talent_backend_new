@extends('layouts.app')

@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div> Inspection Analytics
                <div class="page-title-subheading">Dashboard of inspections, plumbers, and inspectors.</div>
            </div>
        </div>
    </div>
</div>


{{-- Summary --}}
<div class="row">
    <x-dashboard-card icon="bi-person-circle" title="Total Plumbers" value="{{ $totalPlumbers }}" />
    <x-dashboard-card icon="bi-clipboard-data" title="Total Inspections" value="{{ $totalInspections }}" />
    <x-dashboard-card icon="bi-patch-check-fill" title="Approved Inspections" value="{{ $approvedCount }}" />
    <x-dashboard-card icon="bi-person-badge-fill" title="Total Inspectors" value="{{ $totalInspectors }}" />
</div>

{{-- Wallet Summary --}}
<div class="row mt-3">
    <x-dashboard-card icon="bi-cash-stack" title="Instant Withdrawal (EGP)" value="{{ number_format($instantWithdrawalTotal, 2) }}" />
    <x-dashboard-card icon="bi-circle-half" title="Fixed Points" value="{{ $totalFixedPoints }}" />
    <x-dashboard-card icon="bi-gift" title="Gift Points" value="{{ $totalGiftPoints }}" />
<x-dashboard-card icon="bi-box-seam" title="Total Products" value="{{ $totalProducts }}" />

</div>


{{-- Filters --}}
<form method="GET" class="row g-3 align-items-end mb-4">
    <div class="col-md-3">
        <label>City</label>
        <select name="city" class="form-select">
            <option value="">All Cities</option>
            @foreach ($cities as $c)
                <option value="{{ $c }}" {{ $city == $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label>From</label>
        <input type="date" name="from" class="form-control" value="{{ $from }}">
    </div>
    <div class="col-md-3">
        <label>To</label>
        <input type="date" name="to" class="form-control" value="{{ $to }}">
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary w-100">Filter</button>
    </div>
</form>

@if ($city)
    <div class="alert alert-info">Showing data for <strong>{{ $city }}</strong></div>
@endif


{{-- Charts --}}
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Inspection Status Overview</h5>
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Inspections by City</h5>
                <canvas id="cityChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Leaderboards --}}
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Top Plumbers</h5>
                <ul class="list-group">
                    @foreach ($topPlumbers as $plumber)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $plumber->user->name ?? 'Unknown' }}
                            <span class="badge bg-success">{{ $plumber->fixed_points }} pts</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Top Inspectors</h5>
                <ul class="list-group">
                    @foreach ($topInspectors as $inspector)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $inspector['name'] }}
                            <span class="badge bg-primary">{{ $inspector['total'] }} inspections</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
                            
                            
                            <div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-stars"></i> Most Requested Products</h5>
               <ul class="list-group">
    @forelse ($mostRequestedProducts as $product)
        <li class="list-group-item d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                @if ($product['image'])
                    <img src="{{ asset($product['image']) }}" alt="product image" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                @else
                    <i class="bi bi-box-seam fs-4 text-muted"></i>
                @endif
                <span>{{ $product['name'] }}</span>
            </div>
            <span class="badge bg-info">{{ $product['total'] }} requests</span>
        </li>
    @empty
        <li class="list-group-item">No product requests found.</li>
    @endforelse
</ul>

            </div>
        </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const statusChart = new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($inspectionStats->keys()) !!},
            datasets: [{
                label: 'Inspections',
                data: {!! json_encode($inspectionStats->values()) !!},
                backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Status Breakdown'
                }
            }
        }
    });

    const cityChart = new Chart(document.getElementById('cityChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($cityWiseInspections->keys()) !!},
            datasets: [{
                label: 'City Inspections',
                data: {!! json_encode($cityWiseInspections->values()) !!},
                backgroundColor: '#0dcaf0'
            }]
        }
    });
</script>
@endpush
