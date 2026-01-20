@extends('layouts.app')

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            <i class="fa-solid fa-user"></i> {{ __('Employee Details') }}: {{ $user->name }}
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center">
                        <img src="{{ $user->thumbnail }}" class="rounded-circle mb-3" width="150">
                        <h4>{{ $user->fullName }}</h4>
                        <span class="badge text-bg-info mb-3">{{ $role }}</span>
                    </div>
                    <hr>
                    <div class="mt-3">
                        <p><strong>{{ __('Phone') }}:</strong> {{ $user->phone ?? 'N/A' }}</p>
                        <p><strong>{{ __('Email') }}:</strong> {{ $user->email ?? 'N/A' }}</p>
                        <p><strong>{{ __('Gender') }}:</strong> {{ ucfirst($user->gender ?? 'N/A') }}</p>
                        <p><strong>{{ __('Joined') }}:</strong> {{ $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @if ($role === 'envoy')
                <!-- General Overview Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('General Overview') }}</h5>
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
                                        <h6 class="text-muted mb-2">{{ __('Target') }}</h6>
                                        <h3 class="mb-0">{{ number_format($salesStats['overview']['target'], 2) }}</h3>
                                        <small class="text-muted">{{ __('Monthly') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h6 class="text-muted mb-2">{{ __('Total Sales') }}</h6>
                                        <h3 class="mb-0 text-success">{{ number_format($salesStats['sales']['total']['amount'], 2) }}</h3>
                                        <small class="text-muted">{{ __('Current Sum') }}</small>
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

                <!-- Sales Statistics Section -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Sales Statistics') }}</h5>
                        <form action="{{ route('admin.employee.show', $user->id) }}" method="GET" class="d-flex gap-2">
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
                        @if ($salesStats)
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="border rounded p-3 bg-light">
                                        <h6 class="text-muted mb-2">{{ __('Total Sales') }}</h6>
                                        <h3 class="mb-0">{{ number_format($salesStats['sales']['total']['amount'], 2) }}</h3>
                                        <small class="text-muted">{{ $salesStats['sales']['total']['count'] }} {{ __('Visits') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h6 class="text-muted mb-2">{{ __('Direct Sales') }}</h6>
                                        <h3 class="mb-0 text-success">{{ number_format($salesStats['sales']['direct']['amount'], 2) }}</h3>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $salesStats['sales']['direct']['percentage'] }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $salesStats['sales']['direct']['percentage'] }}% {{ __('of total') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h6 class="text-muted mb-2">{{ __('Indirect Sales') }}</h6>
                                        <h3 class="mb-0 text-primary">{{ number_format($salesStats['sales']['indirect']['amount'], 2) }}</h3>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $salesStats['sales']['indirect']['percentage'] }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $salesStats['sales']['indirect']['percentage'] }}% {{ __('of total') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4 text-center">
                                <div class="col-md-6">
                                    <div class="border rounded p-3">
                                        <h6 class="text-muted mb-2">{{ __('Conversion Rate') }}</h6>
                                        <h3 class="mb-0">{{ $salesStats['conversion']['conversion_rate'] }}%</h3>
                                        <small class="text-muted">{{ $salesStats['conversion']['conversions'] }} {{ __('Conversions') }} / {{ $salesStats['conversion']['total_clients'] }} {{ __('Clients') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3">
                                        <h6 class="text-muted mb-2">{{ __('Retention Rate') }}</h6>
                                        <h3 class="mb-0">{{ $salesStats['retention']['retention_rate'] }}%</h3>
                                        <small class="text-muted">{{ $salesStats['retention']['active_clients'] }} {{ __('Active') }} / {{ $salesStats['retention']['total_clients'] }} {{ __('Total') }}</small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-center text-muted py-4">{{ __('No statistics available for this period.') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Visit Timing Section -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Visit Timing Statistics') }}</h5>
                        <form action="{{ route('admin.employee.show', $user->id) }}" method="GET" class="d-flex gap-2">
                            <input type="hidden" name="period" value="{{ request('period', 'week') }}">
                            <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date', now()->toDateString()) }}">
                            <button type="submit" class="btn btn-primary btn-sm">{{ __('Filter') }}</button>
                        </form>
                    </div>

                    <div class="card-body">
                        @if ($timingData && count($timingData['visits']) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Visit ID') }}</th>
                                            <th>{{ __('Client Name') }}</th>
                                            <th>{{ __('Check-in') }}</th>
                                            <th>{{ __('Check-out') }}</th>
                                            <th>{{ __('Duration') }}</th>
                                            <th>{{ __('Gap Since Prev') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($timingData['visits'] as $visit)
                                            <tr>
                                                <td>{{ $visit['visit_id'] }}</td>
                                                <td>{{ $visit['client_name'] }}</td>
                                                <td>{{ \Carbon\Carbon::parse($visit['check_in_at'])->format('H:i:s') }}</td>
                                                <td>{{ $visit['check_out_at'] ? \Carbon\Carbon::parse($visit['check_out_at'])->format('H:i:s') : 'N/A' }}</td>
                                                <td>
                                                    @if ($visit['duration_minutes'] !== null)
                                                        {{ $visit['duration_minutes'] }} {{ __('min') }}
                                                    @else
                                                        <span class="text-muted">{{ __('In Progress') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($visit['time_since_previous_visit_minutes'] !== null)
                                                        {{ $visit['time_since_previous_visit_minutes'] }} {{ __('min') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fa-solid fa-calendar-xmark fa-3x text-muted mb-3"></i>
                                <p class="text-muted">{{ __('No visits found for this date.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <p class="text-muted">{{ __('No additional data available for this role.') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
