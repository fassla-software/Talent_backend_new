@extends('layouts.app')

@push('css')
<style>
    /* ── Stats Cards ──────────────────────────────────────────────────────── */
    .stats-card {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        height: 100%;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    .stats-card:hover { transform: translateY(-5px); }
    .stats-card h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0;
        position: relative;
        z-index: 2;
    }
    .stats-card h5 {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.9;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 2;
    }
    .stats-card .icon {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 4rem;
        opacity: 0.2;
        transform: rotate(-15deg);
    }
    .card-purple  { background: linear-gradient(135deg, #6f42c1, #59359a) !important; }
    .card-success { background: linear-gradient(135deg, #198754, #146c43) !important; }
    .card-info    { background: linear-gradient(135deg, #0dcaf0, #0bacbe) !important; }
    .card-warning { background: linear-gradient(135deg, #ffc107, #ca9a06) !important; }
    .card-danger  { background: linear-gradient(135deg, #dc3545, #b02a37) !important; }
    .card-orange  { background: linear-gradient(135deg, #fd7e14, #ca6510) !important; }

    /* ── Revenue Cards ───────────────────────────────────────────────────── */
    .revenue-card {
        background: white;
        border-radius: 12px;
        padding: 1.2rem;
        border-left: 5px solid #0d6efd;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        height: 100%;
    }
    .revenue-card h3 { margin: 0; color: #333; font-weight: 700; }
    .revenue-card p  { margin: 0; color: #666; font-size: 0.85rem; text-transform: uppercase; }
    .revenue-dr  { border-left-color: #198754; }
    .revenue-ind { border-left-color: #0dcaf0; }

    /* ── Filter ──────────────────────────────────────────────────────────── */
    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    /* ─────────────────────────────────────────────────────────────────────
     * CHART CONTAINERS
     * .chart-wrapper        — plain overflow:hidden box for pie / stacked bar
     * .chart-scroll-wrapper — scrollable viewport for tall horizontal bars
     * ──────────────────────────────────────────────────────────────────── */
    .chart-wrapper {
        width: 100%;
        overflow: hidden;
        position: relative;
    }

    /* Fixed-height viewport; inner chart div grows to fit all bars */
    .chart-scroll-wrapper {
        width: 100%;
        overflow-y: auto;
        overflow-x: hidden;
        position: relative;
        max-height: 340px;
    }
    .chart-scroll-wrapper::-webkit-scrollbar { width: 4px; }
    .chart-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 4px;
    }

    /* Horizontal scroll for wide date ranges (monthly charts) */
    .chart-horizontal-scroll-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        position: relative;
        padding-bottom: 5px;
    }
    .chart-horizontal-scroll-wrapper::-webkit-scrollbar { height: 6px; }
    .chart-horizontal-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 4px;
    }

    /* ── Custom Legend ───────────────────────────────────────────────────── */
    .custom-legend-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
        background: #f8f9fa;
        padding: 8px;
        border-radius: 8px;
        overflow: hidden;
        width: 100%;
    }
    .custom-legend-container {
        display: flex;
        overflow-x: auto;
        gap: 15px;
        scroll-behavior: smooth;
        -ms-overflow-style: none;
        scrollbar-width: none;
        flex-grow: 1;
        padding: 2px 0;
        min-width: 0;
    }
    .custom-legend-container::-webkit-scrollbar { display: none; }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        transition: background 0.2s;
        font-size: 0.85rem;
        color: #555;
    }
    .legend-item:hover  { background: #e9ecef; }
    .legend-item.hidden { opacity: 0.4; text-decoration: line-through; }
    .legend-marker {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        flex-shrink: 0;
    }
    .legend-marker.round { border-radius: 50%; }
    .legend-btn {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #666;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .legend-btn:hover { background: #0d6efd; color: white; border-color: #0d6efd; }
</style>
@endpush

@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div>
                <i class="bi bi-graph-up-arrow me-2"></i> Sales Visits Statistics
                <div class="page-title-subheading">Comprehensive analytics for visits, customers, and revenue.</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Top Row: 6 Metric Cards ──────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stats-card">
            <h5>Total Visits</h5>
            <h2>{{ number_format($totalVisits) }}</h2>
            <div class="icon"><i class="bi bi-calendar-check"></i></div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stats-card card-purple">
            <h5>Sales Rep.</h5>
            <h2>{{ number_format($salesReps) }}</h2>
            <div class="icon"><i class="bi bi-people"></i></div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stats-card card-info">
            <h5>Customers</h5>
            <h2>{{ number_format($totalCustomers) }}</h2>
            <div class="icon"><i class="bi bi-person-lines-fill"></i></div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stats-card card-success">
            <h5>Active</h5>
            <h2>{{ number_format($activeCustomers) }}</h2>
            <div class="icon"><i class="bi bi-person-check-fill"></i></div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stats-card card-warning">
            <h5>Expected</h5>
            <h2>{{ number_format($expectedCustomers) }}</h2>
            <div class="icon"><i class="bi bi-person-plus"></i></div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6">
        <div class="stats-card card-orange">
            <h5>Areas</h5>
            <h2>{{ number_format($uniqueAreasCount) }}</h2>
            <div class="icon"><i class="bi bi-geo-alt"></i></div>
        </div>
    </div>
</div>

{{-- ── Revenue + Filter ─────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.sales-visits-stats.index') }}">
    <div class="row g-3 mb-4 align-items-stretch">
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="revenue-card revenue-dr">
                <p>Dr Revenue</p>
                <h3>{{ number_format($drRevenue, 2) }}</h3>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="revenue-card revenue-ind">
                <p>IND Revenue</p>
                <h3>{{ number_format($indRevenue, 2) }}</h3>
            </div>
        </div>
        <div class="col-xl-8 col-lg-6 col-md-12">
            <div class="filter-section h-100">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">From</label>
                        <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">To</label>
                        <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Sales Rep.</label>
                        <select name="envoy_id" class="form-select form-select-sm">
                            <option value="">All Reps</option>
                            @foreach($envoys as $envoy)
                                <option value="{{ $envoy->id }}" {{ $envoyId == $envoy->id ? 'selected' : '' }}>
                                    {{ $envoy->name }} {{ $envoy->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">CS Type</label>
                        <select name="client_type" class="form-select form-select-sm">
                            <option value="">Both</option>
                            <option value="plumber" {{ $clientType === 'plumber' ? 'selected' : '' }}>Plumber</option>
                            <option value="trader"  {{ $clientType === 'trader'  ? 'selected' : '' }}>Trader</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- ── Charts ───────────────────────────────────────────────────────────── --}}
<div class="row g-3">

    {{-- 1 · Revenue per Customer — scrollable horizontal bar ──────────────── --}}
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <h6 class="card-title fw-bold mb-1">
                    Revenue per Customer
                    <small class="text-muted fw-normal ms-1" style="font-size:.75rem;">scroll ↕</small>
                </h6>
                <div class="chart-scroll-wrapper flex-grow-1">
                    <div id="revenuePerCustomerChart"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2 · Visits by Sales Rep — pie ──────────────────────────────────────── --}}
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title fw-bold">Visits by Sales Rep</h6>
                <div class="chart-wrapper">
                    <div id="visitsByEnvoyChart"></div>
                </div>
                <div class="custom-legend-wrapper" id="legend-envoy">
                    <button class="legend-btn left"><i class="bi bi-chevron-left"></i></button>
                    <div class="custom-legend-container"></div>
                    <button class="legend-btn right"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- 3 · Visits by Customer — scrollable horizontal bar ─────────────────── --}}
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <h6 class="card-title fw-bold mb-1">
                    Visits by Customer
                    <small class="text-muted fw-normal ms-1" style="font-size:.75rem;">scroll ↕</small>
                </h6>
                <div class="chart-scroll-wrapper flex-grow-1">
                    <div id="visitsByCustomerChart"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4 · Customer Statuses — pie ─────────────────────────────────────────── --}}
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title fw-bold">Customer Statuses</h6>
                <div class="chart-wrapper">
                    <div id="customerStatusesChart"></div>
                </div>
                <div class="custom-legend-wrapper" id="legend-status">
                    <button class="legend-btn left"><i class="bi bi-chevron-left"></i></button>
                    <div class="custom-legend-container"></div>
                    <button class="legend-btn right"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- 5 · Customer Interest — pie ─────────────────────────────────────────── --}}
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title fw-bold">Customer Interest</h6>
                <div class="chart-wrapper">
                    <div id="customerInterestChart"></div>
                </div>
                <div class="custom-legend-wrapper" id="legend-interest">
                    <button class="legend-btn left"><i class="bi bi-chevron-left"></i></button>
                    <div class="custom-legend-container"></div>
                    <button class="legend-btn right"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- 6 · Visits per Rep by Status — stacked vertical bar ─────────────────── --}}
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title fw-bold">Visits per Sales Rep (by Status)</h6>
                <div class="chart-wrapper">
                    <div id="visitsPerRepStatusChart"></div>
                </div>
                <div class="custom-legend-wrapper" id="legend-rep-status">
                    <button class="legend-btn left"><i class="bi bi-chevron-left"></i></button>
                    <div class="custom-legend-container"></div>
                    <button class="legend-btn right"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row g-3 mt-1">

    {{-- 7 · Total Approved Visits by Month — bar + average line ────────── --}}
    <div class="col-xl-6 col-lg-12">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title fw-bold">Total Approved Visits by Month</h6>
                <div class="chart-horizontal-scroll-wrapper">
                    <div id="approvedVisitsCountByMonthChart"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 8 · Customer Count by Month, stacked by Status ──────────────────── --}}
    <div class="col-xl-6 col-lg-12">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title fw-bold">Customer Count by Month (by Status)</h6>
                <div class="chart-horizontal-scroll-wrapper">
                    <div id="customerCountByStatusChart"></div>
                </div>
                <div class="custom-legend-wrapper" id="legend-month-status">
                    <button class="legend-btn left"><i class="bi bi-chevron-left"></i></button>
                    <div class="custom-legend-container"></div>
                    <button class="legend-btn right"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- 9 · Direct Sales per Envoy, stacked by Client ───────────────────── --}}
    <div class="col-xl-6 col-lg-12">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <h6 class="card-title fw-bold mb-1">
                    Direct Sales per Sales Rep
                    <small class="text-muted fw-normal ms-1" style="font-size:.75rem;">(stacked by client)</small>
                </h6>
                <div class="chart-wrapper flex-grow-1">
                    <div id="directSalesPerEnvoyChart"></div>
                </div>
                <div class="custom-legend-wrapper" id="legend-direct-sales">
                    <button class="legend-btn left"><i class="bi bi-chevron-left"></i></button>
                    <div class="custom-legend-container"></div>
                    <button class="legend-btn right"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- 10 · Indirect Sales per Envoy, stacked by Client ───────────────── --}}
    <div class="col-xl-6 col-lg-12">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <h6 class="card-title fw-bold mb-1">
                    Indirect Sales per Sales Rep
                    <small class="text-muted fw-normal ms-1" style="font-size:.75rem;">(stacked by client)</small>
                </h6>
                <div class="chart-wrapper flex-grow-1">
                    <div id="indirectSalesPerEnvoyChart"></div>
                </div>
                <div class="custom-legend-wrapper" id="legend-indirect-sales">
                    <button class="legend-btn left"><i class="bi bi-chevron-left"></i></button>
                    <div class="custom-legend-container"></div>
                    <button class="legend-btn right"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- 11 · Inspection Visits Table ─────────────────────────────────────── --}}
<div class="row mt-4 pb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="card-title fw-bold mb-0">Recent Inspection Visits</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="px-4 py-3 border-0">{{ __('Sales Rep.') }}</th>
                                <th class="py-3 border-0">{{ __('Customer') }}</th>
                                <th class="py-3 border-0">{{ __('Type') }}</th>
                                <th class="py-3 border-0">{{ __('Visit Date') }}</th>
                                <th class="py-3 text-center border-0">{{ __('Status') }}</th>
                                <th class="py-3 border-0" style="min-width: 150px;">{{ __('Next Action') }}</th>
                                <th class="py-3 px-4 border-0">{{ __('Notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paginatedVisits as $visit)
                                @php
                                    $customer = $visit->plumber ?? $visit->trader;
                                    $clientTypeLabel = $visit->plumber_id ? 'Plumber' : 'Trader';
                                    $clientTypeClass = $visit->plumber_id ? 'success' : 'primary';
                                    
                                    // Status color logic matching unified users
                                    $status = strtoupper($customer->status ?? 'Unknown');
                                    $statusClass = 'danger';
                                    if (in_array($status, ['ACTIVE', 'APPROVED'])) $statusClass = 'success';
                                    elseif ($status === 'PENDING') $statusClass = 'warning';
                                    elseif ($status === 'DORMANT') $statusClass = 'info';

                                    // Resolved names
                                    $repName = $visit->inspector ? ($visit->inspector->name . ' ' . ($visit->inspector->last_name ?? '')) : 'Unknown';
                                    $custName = $visit->visitReport->customer_name ?? $visit->visitReport->company_name ?? ($customer->user->name ?? 'Unknown');
                                @endphp
                                <tr>
                                    <td class="px-4 fw-semibold text-dark">{{ $repName }}</td>
                                    <td>{{ $custName }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-soft-{{ $clientTypeClass }} text-{{ $clientTypeClass }}" style="font-size: 0.75rem;">
                                            {{ $clientTypeLabel }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap text-muted">
                                        {{ ($visit->scheduled_at ?? $visit->created_at)->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-soft-{{ $statusClass }} text-{{ $statusClass }}" style="font-size: 0.75rem;">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($visit->visitReport && $visit->visitReport->next_action)
                                            <span class="text-dark">{{ $visit->visitReport->next_action }}</span>
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4">
                                        <div class="text-muted small" style="max-width: 250px; white-space: normal;">
                                            {{ $visit->notes ?: '---' }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted italic">
                                        No inspection visits found for the selected criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($paginatedVisits->hasPages())
                <div class="card-footer bg-white py-3 px-4 border-top-0">
                    {{ $paginatedVisits->appends(request()->all())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
{{-- 12 · Customer Visits Pivot Table (Monthly Breakdown) ──────────────── --}}
<div class="row mt-4 pb-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="card-title fw-bold mb-0">Approved Customer Visits Monthly Breakdown (Pivot Table)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="px-3 py-3 border-0 bg-light" style="position: sticky; left: 0; z-index: 10;">{{ __('Customer Name') }}</th>
                                <th class="py-3 border-0 text-center">{{ __('Type') }}</th>
                                @foreach($monthLabels as $month)
                                    <th class="py-3 border-0 text-center text-nowrap" style="min-width: 100px;">{{ $month }}</th>
                                @endforeach
                                <th class="py-3 px-3 border-0 text-center bg-light fw-bold" style="position: sticky; right: 0; z-index: 10;">{{ __('Total Visits') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pivotData as $key => $data)
                                <tr>
                                    <td class="px-3 fw-semibold text-dark bg-white" style="position: sticky; left: 0; z-index: 5; border-right: 1px solid #dee2e6;">
                                        {{ $data['name'] }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-soft-{{ $data['type'] == 'Plumber' ? 'success' : 'primary' }} text-{{ $data['type'] == 'Plumber' ? 'success' : 'primary' }}" style="font-size: 0.7rem;">
                                            {{ $data['type'] }}
                                        </span>
                                    </td>
                                    @foreach($monthLabels as $month)
                                        <td class="text-center">
                                            @if($data['months'][$month] > 0)
                                                <span class="fw-bold text-primary">{{ $data['months'][$month] }}</span>
                                            @else
                                                <span class="text-muted opacity-25">0</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-center px-3 bg-light fw-bold text-dark" style="position: sticky; right: 0; z-index: 5; border-left: 1px solid #dee2e6;">
                                        <div class="badge bg-dark rounded-pill">{{ $data['total'] }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($monthLabels) + 3 }}" class="text-center py-5 text-muted italic">
                                        No data available for the selected period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light fw-bold border-top-2">
                            <tr class="table-dark">
                                <td class="px-3 py-3 border-0 bg-dark text-white" style="position: sticky; left: 0; z-index: 10;">
                                    {{ __('Total / Month') }}
                                </td>
                                <td class="text-center border-0 bg-dark text-white">---</td>
                                @foreach($monthLabels as $month)
                                    <td class="text-center border-0 bg-dark text-white">
                                        {{ $monthlyTotals[$month] > 0 ? $monthlyTotals[$month] : 0 }}
                                    </td>
                                @endforeach
                                <td class="text-center px-3 border-0 bg-primary text-white" style="position: sticky; right: 0; z-index: 10;">
                                    <span style="font-size: 1.1rem;">{{ $grandTotal }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @if($customerPagination->hasPages())
                <div class="card-footer bg-white py-3 px-4 border-top-0">
                    {{ $customerPagination->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- 14 · Sales Value Monthly Breakdown (Pivot Table) ─────────────────── --}}
<div class="row mt-4 pb-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="card-title fw-bold mb-0">Sales Value Monthly Breakdown (Pivot Table)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="px-3 py-3 border-0 bg-light" style="position: sticky; left: 0; z-index: 10; min-width: 150px;">{{ __('Sales Rep') }}</th>
                                <th class="px-3 py-3 border-0 bg-light" style="position: sticky; left: 150px; z-index: 10; min-width: 200px;">{{ __('Customer Name') }}</th>
                                <th class="py-3 border-0 text-center">{{ __('Type') }}</th>
                                @foreach($monthLabels as $month)
                                    <th class="py-3 border-0 text-center text-nowrap" style="min-width: 100px;">{{ $month }}</th>
                                @endforeach
                                <th class="py-3 px-3 border-0 text-center bg-light fw-bold" style="position: sticky; right: 0; z-index: 10;">{{ __('Total Sales') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $processedReps = [];
                                $repCounts = [];
                                foreach($salesValuePivotData as $item) {
                                    $rName = $item['rep_name'];
                                    $repCounts[$rName] = ($repCounts[$rName] ?? 0) + 1;
                                }
                            @endphp
                            @forelse($salesValuePivotData as $key => $data)
                                <tr>
                                    @if(!isset($processedReps[$data['rep_name']]))
                                        <td class="px-3 fw-semibold text-dark bg-white" 
                                            rowspan="{{ $repCounts[$data['rep_name']] }}"
                                            style="position: sticky; left: 0; z-index: 5; border-right: 1px solid #dee2e6; vertical-align: middle;">
                                            {{ $data['rep_name'] }}
                                        </td>
                                        @php $processedReps[$data['rep_name']] = true; @endphp
                                    @endif
                                    <td class="px-3 fw-semibold text-dark bg-white" style="position: sticky; left: 150px; z-index: 5; border-right: 1px solid #dee2e6;">
                                        {{ $data['cust_name'] }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-soft-{{ $data['type'] == 'Plumber' ? 'success' : 'primary' }} text-{{ $data['type'] == 'Plumber' ? 'success' : 'primary' }}" style="font-size: 0.7rem;">
                                            {{ $data['type'] }}
                                        </span>
                                    </td>
                                    @foreach($monthLabels as $month)
                                        <td class="text-center">
                                            @if($data['months'][$month] > 0)
                                                <span class="fw-bold text-success">{{ number_format($data['months'][$month], 2) }}</span>
                                            @else
                                                <span class="text-muted opacity-25">0.00</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-center px-3 bg-light fw-bold text-dark" style="position: sticky; right: 0; z-index: 5; border-left: 1px solid #dee2e6;">
                                        <div class="badge bg-success rounded-pill">{{ number_format($data['total'], 2) }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($monthLabels) + 4 }}" class="text-center py-5 text-muted italic">
                                        No data available for the selected period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light fw-bold border-top-2">
                            <tr class="table-dark">
                                <td colspan="2" class="px-3 py-3 border-0 bg-dark text-white" style="position: sticky; left: 0; z-index: 10;">
                                    {{ __('Total Sales / Month') }}
                                </td>
                                <td class="text-center border-0 bg-dark text-white">---</td>
                                @foreach($monthLabels as $month)
                                    <td class="text-center border-0 bg-dark text-white">
                                        {{ number_format($monthlySalesTotals[$month], 2) }}
                                    </td>
                                @endforeach
                                <td class="text-center px-3 border-0 bg-primary text-white" style="position: sticky; right: 0; z-index: 10;">
                                    <span style="font-size: 1.1rem;">{{ number_format($grandSalesTotal, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @if($salesPagination->hasPages())
                <div class="card-footer bg-white py-3 px-4 border-top-0">
                    {{ $salesPagination->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
{{-- 13 · Approved Visits by Month, stacked by Envoy (Full Width at Bottom) ── --}}
<div class="row mt-4 pb-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex flex-column" style="min-height: 450px;">
                <h6 class="card-title fw-bold mb-1">
                    Approved Visits by Month
                    <small class="text-muted fw-normal ms-1" style="font-size:.75rem;">(stacked by envoy)</small>
                </h6>
                <div class="chart-horizontal-scroll-wrapper flex-grow-1">
                    <div id="approvedVisitsByEnvoyChart"></div>
                </div>
                <div class="custom-legend-wrapper" id="legend-approved-visits-envoy">
                    <button class="legend-btn left"><i class="bi bi-chevron-left"></i></button>
                    <div class="custom-legend-container"></div>
                    <button class="legend-btn right"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Colour palette ─────────────────────────────────────────────────────
    const DEFAULT_COLORS    = ['#0d6efd','#6f42c1','#198754','#0dcaf0','#ffc107','#dc3545','#fd7e14','#20c997','#084298','#6610f2'];
    const STATUS_COLORS     = ['#198754','#ffc107','#0dcaf0','#6c757d'];
    const BAR_STATUS_COLORS = ['#198754','#ffc107','#0dcaf0','#6c757d','#adb5bd'];

    // ── Option builders ────────────────────────────────────────────────────

    /**
     * Horizontal bar — height auto-grows so every bar has room.
     * width:'100%' is safe for horizontal bars (they never over-expand).
     * The parent .chart-scroll-wrapper clips the height to a viewport.
     */
    function hBarOptions(labels, values, color) {
        const BAR_H = 32;
        const h     = Math.max(labels.length * BAR_H + 40, 120);
        return {
            chart: {
                type: 'bar',
                height: h,
                width: '100%',
                parentHeightOffset: 0,
                toolbar: { show: false },
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '65%',
                    borderRadius: 4,
                    dataLabels: { position: 'right' },
                }
            },
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                offsetX: 6,
                style: { fontSize: '11px', fontWeight: 400, colors: ['#333'] },
                formatter: val => Number(val).toLocaleString(),
            },
            xaxis: {
                categories: labels,
                labels: {
                    formatter: val => Number(val).toLocaleString(),
                    style: { fontSize: '11px' },
                },
            },
            yaxis: {
                labels: {
                    style: { fontSize: '11px' },
                    maxWidth: 150,
                },
            },
            series: [{ name: 'Value', data: values }],
            colors: [color],
            grid: { borderColor: '#f1f1f1', padding: { right: 20 } },
            tooltip: { y: { formatter: val => Number(val).toLocaleString() } },
            legend: { show: false },
        };
    }

    /**
     * Pie — measures container width AFTER the grid has settled.
     * This is the root-cause fix: never let ApexCharts guess its own width.
     */
    function pieOptions(selector, labels, series, colors) {
        const el = document.querySelector(selector);
        const w  = el ? (el.closest('.chart-wrapper') || el.parentElement).clientWidth - 8 : 340;
        return {
            chart: {
                type: 'pie',
                height: 300,
                width: w,
                parentHeightOffset: 0,
                toolbar: { show: false },
            },
            labels,
            series,
            colors,
            dataLabels: {
                enabled: true,
                formatter: (_v, opts) =>
                    Number(opts.w.config.series[opts.seriesIndex]).toLocaleString(),
            },
            legend: { show: false },
            responsive: [{ breakpoint: 10000, options: { chart: { width: w } } }],
        };
    }

    /**
     * Stacked vertical bar — width:'100%' is fine for vertical bars.
     */
    function stackedBarOptions(labels, series, colors) {
        return {
            chart: {
                type: 'bar',
                height: 300,
                width: '100%',
                stacked: true,
                parentHeightOffset: 0,
                toolbar: { show: false },
            },
            plotOptions: { bar: { horizontal: false, columnWidth: '55%' } },
            dataLabels: { enabled: false },
            xaxis: { categories: labels, labels: { style: { fontSize: '11px' } } },
            series,
            colors,
            grid: { borderColor: '#f1f1f1' },
            legend: { show: false },
        };
    }

    // ── Legend builder ─────────────────────────────────────────────────────
    function initLegend(chart, labels, colors, wrapperId, isPie) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;
        const container = wrapper.querySelector('.custom-legend-container');
        const btnL      = wrapper.querySelector('.legend-btn.left');
        const btnR      = wrapper.querySelector('.legend-btn.right');

        labels.forEach((label, i) => {
            const item = document.createElement('div');
            item.className = 'legend-item';
            item.innerHTML =
                `<span class="legend-marker ${isPie ? 'round' : ''}"
                       style="background:${colors[i % colors.length]}"></span>
                 <span>${label}</span>`;
            item.addEventListener('click', () => {
                isPie ? chart.toggleDataPointSelection(i) : chart.toggleSeries(label);
                item.classList.toggle('hidden');
            });
            container.appendChild(item);
        });

        btnL.addEventListener('click', () => container.scrollBy({ left: -200, behavior: 'smooth' }));
        btnR.addEventListener('click', () => container.scrollBy({ left:  200, behavior: 'smooth' }));
    }

    // ── Sort helper ────────────────────────────────────────────────────────
    function sortedDesc(labels, values) {
        const pairs = labels.map((l, i) => [l, values[i]]).sort((a, b) => b[1] - a[1]);
        return { labels: pairs.map(p => p[0]), values: pairs.map(p => p[1]) };
    }

    // ──────────────────────────────────────────────────────────────────────
    // Render everything AFTER Bootstrap grid has calculated column widths.
    // 200 ms is enough on any normal machine/connection.
    // ──────────────────────────────────────────────────────────────────────
    // ──────────────────────────────────────────────────────────────────────
    // Render everything AFTER Bootstrap grid has calculated column widths.
    // 200 ms is enough on any normal machine/connection.
    // ──────────────────────────────────────────────────────────────────────
    setTimeout(function () {

        // ── Helper: build stacked series from { axis_key: { stack_key: value } } ──
        function buildStackedChartOptions(rawData, chartHeight) {
            if (!rawData || typeof rawData !== 'object' || Object.keys(rawData).length === 0) {
                return { options: { chart: { height: chartHeight, width: '100%' }, series: [] }, keys: [], palette: [] };
            }
            const xCategories = Object.keys(rawData);
            const stackKeys   = [...new Set(xCategories.flatMap(k => Object.keys(rawData[k] || {})))].sort();
            const palette     = [
                '#0d6efd','#6f42c1','#198754','#0dcaf0','#ffc107','#dc3545',
                '#fd7e14','#20c997','#084298','#6610f2','#d63384','#48cae4',
            ];
            const series = stackKeys.map((key, i) => ({
                name: key,
                data: xCategories.map(cat => (rawData[cat] ? rawData[cat][key] || 0 : 0)),
            }));

            // Dynamic width for monthly charts
            const minMonthWidth = 80;
            const computedWidth = Math.max(100, xCategories.length * minMonthWidth);
            const finalWidth    = xCategories.length > 8 ? `${computedWidth}px` : '100%';

            return {
                options: {
                    chart: { type: 'bar', height: chartHeight, width: finalWidth, stacked: true, toolbar: { show: false }, parentHeightOffset: 0 },
                    plotOptions: { bar: { columnWidth: '55%', borderRadius: 2 } },
                    dataLabels: { enabled: false },
                    xaxis: { categories: xCategories, labels: { style: { fontSize: '11px' } } },
                    yaxis: { labels: { formatter: val => Number(val).toLocaleString(), style: { fontSize: '11px' } } },
                    series,
                    colors: palette,
                    grid: { borderColor: '#f1f1f1' },
                    tooltip: { y: { formatter: val => Number(val).toLocaleString() } },
                    legend: { show: false },
                },
                keys: stackKeys,
                palette,
            };
        }

        // 1 · Revenue per Customer
        const revRaw    = {!! json_encode($revenuePerCustomer) !!} || {};
        const revSorted = sortedDesc(Object.keys(revRaw), Object.values(revRaw).map(Number));
        new ApexCharts(
            document.querySelector('#revenuePerCustomerChart'),
            hBarOptions(revSorted.labels, revSorted.values, '#0d6efd')
        ).render();

        // 2 · Visits by Sales Rep
        const envoyRaw  = {!! json_encode($visitsByEnvoy) !!} || {};
        const envoyLabels = Object.keys(envoyRaw);
        const envoySeries = Object.values(envoyRaw).map(Number);
        const envoyChart  = new ApexCharts(
            document.querySelector('#visitsByEnvoyChart'),
            pieOptions('#visitsByEnvoyChart', envoyLabels, envoySeries, DEFAULT_COLORS)
        );
        envoyChart.render();
        initLegend(envoyChart, envoyLabels, DEFAULT_COLORS, 'legend-envoy', true);

        // 3 · Visits by Customer
        const custRaw    = {!! json_encode($visitsByCustomer) !!} || {};
        const custSorted = sortedDesc(Object.keys(custRaw), Object.values(custRaw).map(Number));
        new ApexCharts(
            document.querySelector('#visitsByCustomerChart'),
            hBarOptions(custSorted.labels, custSorted.values, '#6f42c1')
        ).render();

        // 4 · Customer Statuses
        const statusRaw    = {!! json_encode($statusCounts) !!} || {};
        const statusLabels = Object.keys(statusRaw);
        const statusValues = Object.values(statusRaw).map(Number);
        const statusChart  = new ApexCharts(
            document.querySelector('#customerStatusesChart'),
            pieOptions('#customerStatusesChart', statusLabels, statusValues, STATUS_COLORS)
        );
        statusChart.render();
        initLegend(statusChart, statusLabels, STATUS_COLORS, 'legend-status', true);

        // 5 · Customer Interest
        const intRaw    = {!! json_encode($customerInterest) !!} || {};
        const intLabels = Object.keys(intRaw);
        const intSeries = Object.values(intRaw).map(Number);
        const intChart  = new ApexCharts(
            document.querySelector('#customerInterestChart'),
            pieOptions('#customerInterestChart', intLabels, intSeries, DEFAULT_COLORS)
        );
        intChart.render();
        initLegend(intChart, intLabels, DEFAULT_COLORS, 'legend-interest', true);

        // 6 · Visits per Rep by Status
        const repData      = {!! json_encode($visitsPerRepByStatus) !!} || {};
        const allStatuses  = ['ACTIVE', 'PENDING', 'INACTIVE', 'DORMANT', 'Unknown'];
        const reps         = Object.keys(repData);
        const stackSeries  = allStatuses.map(s => ({
            name: s,
            data: reps.map(r => repData[r] ? repData[r][s] || 0 : 0),
        }));
        const repChart = new ApexCharts(
            document.querySelector('#visitsPerRepStatusChart'),
            stackedBarOptions(reps, stackSeries, BAR_STATUS_COLORS)
        );
        repChart.render();
        initLegend(repChart, allStatuses, BAR_STATUS_COLORS, 'legend-rep-status', false);

        // 7 · Total Approved Visits by Month
        const appMonthData = {!! json_encode($approvedVisitMonthData) !!} || { months: [], counts: [], average: 0, byStatus: {} };
        const monthCount   = (appMonthData.months || []).length;
        const chartWidth   = monthCount > 8 ? (monthCount * 80) + 'px' : '100%';

        new ApexCharts(document.querySelector('#approvedVisitsCountByMonthChart'), {
            chart: { type: 'bar', height: 300, width: chartWidth, toolbar: { show: false }, parentHeightOffset: 0 },
            plotOptions: { bar: { columnWidth: '55%', borderRadius: 4 } },
            dataLabels: { enabled: false },
            xaxis: { categories: appMonthData.months, labels: { style: { fontSize: '11px' } } },
            yaxis: { labels: { style: { fontSize: '11px' } } },
            series: [{ name: 'Approved Visits', data: appMonthData.counts }],
            colors: ['#0d6efd'],
            grid: { borderColor: '#f1f1f1' },
            annotations: {
                yaxis: [{
                    y: appMonthData.average,
                    borderColor: '#dc3545',
                    strokeDashArray: 5,
                    label: {
                        text: 'Avg: ' + Number(appMonthData.average).toLocaleString(),
                        style: { color: '#fff', background: '#dc3545', fontSize: '11px' },
                        position: 'right',
                        offsetX: -10,
                    }
                }]
            },
            tooltip: { y: { formatter: val => Number(val).toLocaleString() } },
            legend: { show: false },
        }).render();

        // 8 · Customer Count by Month by Status
        const monthStatuses = ['ACTIVE', 'PENDING', 'INACTIVE', 'DORMANT'];
        const monthStatusSeries = monthStatuses.map(s => ({
            name: s,
            data: appMonthData.months.map((m, i) => (appMonthData.byStatus[s] ? appMonthData.byStatus[s][i] || 0 : 0)),
        }));
        const monthStatusChart = new ApexCharts(
            document.querySelector('#customerCountByStatusChart'),
            Object.assign(stackedBarOptions(appMonthData.months, monthStatusSeries, STATUS_COLORS), {
                chart: { ...stackedBarOptions(appMonthData.months, monthStatusSeries, STATUS_COLORS).chart, width: chartWidth }
            })
        );
        monthStatusChart.render();
        initLegend(monthStatusChart, monthStatuses, STATUS_COLORS, 'legend-month-status', false);

        // 9 · Direct Sales per Envoy
        const directRaw   = {!! json_encode($directSalesPerEnvoy) !!} || {};
        const directBuilt = buildStackedChartOptions(directRaw, 320);
        const directChart = new ApexCharts(document.querySelector('#directSalesPerEnvoyChart'), directBuilt.options);
        directChart.render();
        initLegend(directChart, directBuilt.keys, directBuilt.palette, 'legend-direct-sales', false);

        // 10 · Indirect Sales per Envoy
        const indirectRaw   = {!! json_encode($indirectSalesPerEnvoy) !!} || {};
        const indirectBuilt = buildStackedChartOptions(indirectRaw, 320);
        const indirectChart = new ApexCharts(document.querySelector('#indirectSalesPerEnvoyChart'), indirectBuilt.options);
        indirectChart.render();
        initLegend(indirectChart, indirectBuilt.keys, indirectBuilt.palette, 'legend-indirect-sales', false);

        // 13 · Approved Visits by Month stacked by Envoy
        const appEnvoyRaw  = {!! json_encode($approvedVisitsByEnvoyStacked) !!} || {};
        const appEnvoyBuilt = buildStackedChartOptions(appEnvoyRaw, 320);
        const appEnvoyChart = new ApexCharts(document.querySelector('#approvedVisitsByEnvoyChart'), appEnvoyBuilt.options);
        appEnvoyChart.render();
        initLegend(appEnvoyChart, appEnvoyBuilt.keys, appEnvoyBuilt.palette, 'legend-approved-visits-envoy', false);

        // Final safety
        window.dispatchEvent(new Event('resize'));

    }, 200);

})();
</script>
@endpush