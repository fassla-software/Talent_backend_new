@extends('layouts.app')

@php
    $currentYearMonth = now()->format('Y-m');
    $earnedMetricAwards = [];
    foreach($envoyAwards as $award) {
        if (str_contains($award['reason'], 'Automatic:') && str_contains($award['reason'], $currentYearMonth)) {
            // Extract metric name from "Automatic: [metric] Target Met - YYYY-MM"
            // Reason format: "Automatic: sales Target Met - 2026-02"
            preg_match('/Automatic: (\w+) Target Met/', $award['reason'], $matches);
            if (isset($matches[1])) {
                $earnedMetricAwards[strtolower($matches[1])] = true;
            }
        }
    }
@endphp

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center justify-content-between">
            <div class="d-flex gap-2 align-items-center">
                <i class="fa-solid fa-user"></i> {{ __('Envoy Details') }}: {{ $user->name }}
            </div>
            <a href="{{ route('admin.envoy.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left"></i> {{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center">
                        <img src="{{ $user->thumbnail }}" class="rounded-circle mb-3" width="150">
                        <h4>{{ $user->fullName }}</h4>
                        <span class="badge text-bg-info mb-3">Envoy</span>
                    </div>
                    <hr>
                    <div class="mt-3">
                        <p><strong>{{ __('Phone') }}:</strong> {{ $user->phone ?? 'N/A' }}</p>
                        <p><strong>{{ __('Email') }}:</strong> {{ $user->email ?? 'N/A' }}</p>
                        <p><strong>{{ __('Region') }}:</strong> {{ $user->envoySetting->region ?? 'N/A' }}</p>
                        <p><strong>{{ __('Joined') }}:</strong> {{ $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A' }}</p>
                        <hr>
                        <p><strong>{{ __('Salary') }}:</strong> {{ number_format($user->envoySetting->salary ?? 0, 2) }}</p>
                        <p><strong>{{ __('Incentives') }}:</strong> {{ number_format($user->envoySetting->incentives ?? 0, 2) }}</p>
                        <p><strong>{{ __('Target') }}:</strong> {{ number_format($user->envoySetting->target ?? 0, 2) }}</p>
{{-- <p><strong>{{ __('Weight') }}:</strong> {{ $user->envoySetting->weight ?? 0 }}</p> --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- General Overview Section (Fixed at top) -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('General Overview') }}</h5>
                    <form action="{{ route('admin.envoy.show', $user->id) }}" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="date" value="{{ request('date', now()->toDateString()) }}">
                        <select name="period" class="form-control form-control-sm">
                            <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>{{ __('This Week') }}</option>
                            <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>{{ __('This Month') }}</option>
                            <option value="quarter" {{ request('period') == 'quarter' ? 'selected' : '' }}>{{ __('This Quarter') }}</option>
                            <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>{{ __('This Year') }}</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('Filter') }}</button>
                    </form>
                </div>
                <div class="card-body">
                    @if ($salesStats && isset($salesStats['overview']))
                        <!-- Performance Metrics Row -->
                        <div class="row text-center mb-4">
                            <div class="col-md-4">
                                <div class="border rounded p-3 bg-light">
                                    <h6 class="text-muted mb-2">{{ __('Visits') }}</h6>
                                    <h3 class="mb-0 text-primary">{{ $salesStats['overview']['total_visits'] }}</h3>
                                    <small class="text-muted">{{ $salesStats['overview']['approved_visits'] }} {{ __('Approved') }}</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3">
                                    <h6 class="text-muted mb-2">{{ __('Target Sales') }}</h6>
                                    <h3 class="mb-0">{{ number_format($user->envoySetting->target_sales ?? 0, 2) }}</h3>
                                    <small class="text-muted">{{ __('Monthly') }}</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 bg-primary text-white">
                                    <h6 class="text-white-50 mb-2">{{ __('Performance Score') }}</h6>
                                    <h3 class="mb-0">{{ number_format($salesStats['performance_score'] ?? 0, 1) }}%</h3>
                                    <small class="text-white-50">{{ __('Weighted Average') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Client Status Breakdown Row -->
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <h6 class="text-muted mb-2 text-success">{{ __('Active') }}</h6>
                                    <h3 class="mb-0 text-success">{{ $salesStats['overview']['active_count'] }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <h6 class="text-muted mb-2 text-warning">{{ __('Inactive') }}</h6>
                                    <h3 class="mb-0 text-warning">{{ $salesStats['overview']['inactive_count'] }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <h6 class="text-muted mb-2 text-danger">{{ __('Dormant') }}</h6>
                                    <h3 class="mb-0 text-danger">{{ $salesStats['overview']['dormant_count'] }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <h6 class="text-muted mb-2 text-info">{{ __('Pending') }}</h6>
                                    <h3 class="mb-0 text-info">{{ $salesStats['overview']['pending_count'] }}</h3>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-center text-muted py-4">{{ __('No overview data available.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Detailed Tabs Section -->
            <ul class="nav nav-tabs mb-3" id="envoyDetailTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="achievements-tab" data-bs-toggle="tab" data-bs-target="#achievements" type="button" role="tab" aria-controls="achievements" aria-selected="true">
                        <i class="fa-solid fa-medal me-1"></i> {{ __('Achievements') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button" role="tab" aria-controls="stats" aria-selected="false">
                        <i class="fa-solid fa-chart-pie me-1"></i> {{ __('Statistics') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="false">
                        <i class="fa-solid fa-clock-rotate-left me-1"></i> {{ __('Activity') }}
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="envoyDetailTabsContent">
                <!-- Achievements Tab -->
                <div class="tab-pane fade show active" id="achievements" role="tabpanel" aria-labelledby="achievements-tab">
                    <div class="card mb-4">
                        <div class="card-body">
                            @if (isset($salesStats['performance_details']) && count($salesStats['performance_details']) > 0)
                                <h6 class="mb-3">{{ __('Metric Achievement Details') }}</h6>
                                <div class="row">
                                    @foreach ($salesStats['performance_details'] as $metric)
                                        <div class="col-md-3 mb-3">
                                            <div class="border rounded p-2 text-center {{ $metric['is_exceeded'] ? 'bg-success-subtle border-success' : '' }}">
                                                <small class="text-muted d-block text-uppercase mb-1" style="font-size: 0.7rem;">{{ __($metric['key']) }}</small>
                                                <h5 class="mb-1 {{ $metric['is_exceeded'] ? 'text-success' : '' }}">
                                                    {{ number_format($metric['achievement_percent'], 1) }}%
                                                </h5>
                                                @if ($metric['is_exceeded'])
                                                    <span class="badge bg-success" style="font-size: 0.6rem;">{{ __('Target Exceeded!') }}</span>
                                                @endif

                                                @if (isset($earnedMetricAwards[strtolower($metric['key'])]))
                                                    <div class="mt-1">
                                                        <span class="badge bg-warning text-dark" style="font-size: 0.6rem;">
                                                            <i class="fa-solid fa-trophy"></i> {{ __('Reward Earned!') }}
                                                        </span>
                                                    </div>
                                                @endif
                                                <div class="mt-2 text-muted" style="font-size: 0.7rem;">
                                                    {{ __('Target') }}: {{ $metric['target'] }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center text-muted py-3">{{ __('No achievement data available.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Statistics Tab -->
                <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                    <div class="card mb-4">
                        <div class="card-body">
                            @if ($salesStats)
                                <div class="row text-center mb-4">
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 bg-light">
                                            <h6 class="text-muted mb-2">{{ __('Total Sales') }}</h6>
                                            <h3 class="mb-0">{{ number_format($salesStats['sales']['total']['amount'], 2) }}</h3>
                                            <small class="text-muted">{{ $salesStats['sales']['total']['count'] }} {{ __('Invoices') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3">
                                            <h6 class="text-muted mb-2">{{ __('Direct Sales') }}</h6>
                                            <h3 class="mb-0 text-success">{{ number_format($salesStats['sales']['direct']['amount'], 2) }}</h3>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $salesStats['sales']['direct']['percentage'] }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $salesStats['sales']['direct']['percentage'] }}%</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3">
                                            <h6 class="text-muted mb-2">{{ __('Indirect Sales') }}</h6>
                                            <h3 class="mb-0 text-primary">{{ number_format($salesStats['sales']['indirect']['amount'], 2) }}</h3>
                                            <div class="progress mt-2" style="height: 5px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $salesStats['sales']['indirect']['percentage'] }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $salesStats['sales']['indirect']['percentage'] }}%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-md-6">
                                        <div class="border rounded p-3">
                                            <h6 class="text-muted mb-2">{{ __('Conversion Rate') }}</h6>
                                            <h3 class="mb-0">{{ $salesStats['conversion']['conversion_rate'] }}%</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="border rounded p-3">
                                            <h6 class="text-muted mb-2">{{ __('Retention Rate') }}</h6>
                                            <h3 class="mb-0">{{ $salesStats['retention']['retention_rate'] }}%</h3>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-center text-muted py-3">{{ __('No statistics available.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Activity Tab -->
                <div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                    <!-- Visit Timing Section -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <h6 class="mb-0">{{ __('Visit Timing') }}</h6>
                            <form action="{{ route('admin.envoy.show', $user->id) }}" method="GET" class="d-flex gap-2">
                                <input type="hidden" name="period" value="{{ request('period', 'week') }}">
                                <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date', now()->toDateString()) }}">
                                <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('Go') }}</button>
                            </form>
                        </div>
                        <div class="card-body">
                            @if ($timingData && count($timingData['visits']) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Client') }}</th>
                                                <th>{{ __('Check-in') }}</th>
                                                <th>{{ __('Duration') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($timingData['visits'] as $visit)
                                                <tr>
                                                    <td>{{ Str::limit($visit['client_name'], 20) }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($visit['check_in_at'])->format('H:i') }}</td>
                                                    <td>{{ $visit['duration_minutes'] ?? '-' }} min</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if (isset($timingPaginator))
                                    <div class="mt-3 d-flex justify-content-center">
                                        <div class="pagination-sm">
                                            {{ $timingPaginator->appends(request()->except('timing_page'))->links() }}
                                        </div>
                                    </div>
                                @endif
                            @else
                                <p class="text-center text-muted py-2">{{ __('No timing data for this date.') }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Visits Section -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{ __('Recent Visits') }}</h6>
                        </div>
                        <div class="card-body p-0">
                            @if (isset($visits) && count($visits) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0 text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Client') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($visits as $visit)
                                                <tr>
                                                    <td>{{ Str::limit($visit['company_name'] ?? __('N/A'), 15) }}</td>
                                                    <td>
                                                        <span class="badge {{ $visit['status'] == 'APPROVED' ? 'bg-success' : ($visit['status'] == 'REJECTED' ? 'bg-danger' : 'bg-warning') }} p-1" style="font-size: 0.65rem;">
                                                            {{ $visit['status'] }}
                                                        </span>
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($visit['date'])->format('m-d H:i') }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.inspectionVisit.show', $visit['id']) }}" class="text-info"><i class="fa fa-eye"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if (isset($visitsPaginator))
                                    <div class="p-3 d-flex justify-content-center">
                                        <div class="pagination-sm">
                                            {{ $visitsPaginator->appends(request()->except('visits_page'))->links() }}
                                        </div>
                                    </div>
                                @endif
                            @else
                                <p class="text-center text-muted py-3">{{ __('No recent visits.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Awards & Recognition Section -->
            <div class="card mt-4">
                <div class="card-header bg-warning-subtle">
                    <h6 class="mb-0"><i class="fa-solid fa-award me-1"></i> {{ __('Awards & Recognition') }}</h6>
                </div>
                <div class="card-body p-0">
                    @if (count($envoyAwards) > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($envoyAwards as $award)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0 text-primary">{{ $award['award']['title'] }}</h6>
                                            <small class="text-muted">{{ $award['reason'] }}</small>
                                        </div>
                                        <span class="badge bg-light text-dark border">{{ \Carbon\Carbon::parse($award['created_at'])->format('Y-m-d') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-muted py-4 m-0">{{ __('No awards received yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        (function() {
            const storageKey = 'envoy_show_active_tab';
            
            function initTabs() {
                const activeTabId = localStorage.getItem(storageKey);
                if (activeTabId) {
                    const tabTrigger = document.getElementById(activeTabId);
                    if (tabTrigger) {
                        const tab = new bootstrap.Tab(tabTrigger);
                        tab.show();
                    }
                }

                document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(btn => {
                    btn.addEventListener('shown.bs.tab', (e) => {
                        localStorage.setItem(storageKey, e.target.id);
                    });
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTabs);
            } else {
                initTabs();
            }
        })();
    </script>
@endsection
