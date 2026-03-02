@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between mb-4">
        <h4>{{ __('Envoy Management') }}</h4>
        <a href="{{ route('admin.envoy.create') }}" class="btn btn-primary py-2.5">
            <i class="fa fa-plus-circle"></i>
            {{ __('Add Envoy') }}
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4 g-3">
        <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="card-title opacity-75">{{ __('Total Envoys') }}</h6>
                    <h3 class="mb-0 fw-bold">{{ $totalEnvoys }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="card-title opacity-75">{{ __('Total Visits') }}</h6>
                    <h3 class="mb-0 fw-bold">{{ $totalVisits }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="card-title opacity-75">{{ __('Performance') }}</h6>
                    <h3 class="mb-0 fw-bold">{{ $performanceAvg }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <h6 class="card-title opacity-75">{{ __('Total Sells Target') }}</h6>
                    <h3 class="mb-0 fw-bold">{{ number_format($totalSellsTarget, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <div class="card bg-secondary text-white h-100">
                <div class="card-body">
                    <h6 class="card-title opacity-75">{{ __('Conversion') }}</h6>
                    <h3 class="mb-0 fw-bold">{{ $conversionAvg }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4 col-6">
            <div class="card bg-dark text-white h-100">
                <div class="card-body">
                    <h6 class="card-title opacity-75">{{ __('Retention') }}</h6>
                    <h3 class="mb-0 fw-bold">{{ $retentionAvg }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.envoy.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="form-label">{{ __('Search') }}</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Search by name or phone number" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="period" class="form-label">{{ __('Stats Period') }}</label>
                        <select name="period" id="period" class="form-select">
                            <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>{{ __('This Week') }}</option>
                            <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>{{ __('This Month') }}</option>
                            <option value="quarter" {{ request('period') == 'quarter' ? 'selected' : '' }}>{{ __('This Quarter') }}</option>
                            <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>{{ __('This Year') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Envoys Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table border table-responsive-lg">
                    <thead>
                        <tr>
                            <th class="text-center">{{ __('SL') }}.</th>
                            <th>{{ __('Profile') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Region') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $key => $user)
                            <tr>
                                <td class="text-center">{{ $users->firstItem() + $key }}</td>
                                <td>
                                    <img src="{{ $user->thumbnail }}" width="50" class="rounded-circle">
                                </td>
                                <td>{{ Str::limit($user->fullName, 50, '...') }}</td>
                                <td>{{ $user->phone ?? 'N/A' }}</td>
                                <td>{{ $user->email ?? 'N/A' }}</td>
                                <td>{{ $user->envoySetting->region ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('admin.envoy.show', $user->id) }}" class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.envoy.edit', $user->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.envoy.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="7">{{ __('No Envoys Found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="my-3">
        {{ $users->withQueryString()->links() }}
    </div>
@endsection
