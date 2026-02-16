@extends('layouts.app')

@section('title', __('Users Information'))

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">{{ __('Traders') }}</h6>
                        <h2 class="mb-0">{{ $stats['traders'] }}</h2>
                    </div>
                    <i class="fa fa-handshake fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">{{ __('Plumbers') }}</h6>
                        <h2 class="mb-0">{{ $stats['plumbers'] }}</h2>
                    </div>
                    <i class="fa fa-tools fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white mb-4 {{ $status_filter ? 'opacity-50' : '' }}" style="{{ $status_filter ? 'filter: grayscale(1); pointer-events: none;' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">{{ __('Distributors') }}</h6>
                        <h2 class="mb-0">{{ $stats['distributors'] }}</h2>
                    </div>
                    <i class="fa fa-truck fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white mb-4 {{ $status_filter ? 'opacity-50' : '' }}" style="{{ $status_filter ? 'filter: grayscale(1); pointer-events: none;' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">{{ __('Envoys') }}</h6>
                        <h2 class="mb-0">{{ $stats['envoys'] }}</h2>
                    </div>
                    <i class="fa fa-user-tie fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card bg-white border mb-4 {{ $status_filter == 'PENDING' ? 'border-warning' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 text-muted">{{ __('Pending') }}</h6>
                        <h2 class="mb-0 text-warning">{{ $stats['status_counts']['pending'] }}</h2>
                    </div>
                    <i class="fa fa-clock fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-white border mb-4 {{ $status_filter == 'ACTIVE' ? 'border-success' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 text-muted">{{ __('Active') }}</h6>
                        <h2 class="mb-0 text-success">{{ $stats['status_counts']['active'] }}</h2>
                    </div>
                    <i class="fa fa-check-circle fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-white border mb-4 {{ $status_filter == 'INACTIVE' ? 'border-danger' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 text-muted">{{ __('Inactive') }}</h6>
                        <h2 class="mb-0 text-danger">{{ $stats['status_counts']['inactive'] }}</h2>
                    </div>
                    <i class="fa fa-times-circle fa-2x text-danger opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-white border mb-4 {{ $status_filter == 'DORMANT' ? 'border-info' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 text-muted">{{ __('Dormant') }}</h6>
                        <h2 class="mb-0 text-info">{{ $stats['status_counts']['dormant'] }}</h2>
                    </div>
                    <i class="fa fa-moon fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <form action="{{ route('admin.users-information.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('Search by name or phone...') }}" value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ __('All Roles') }}</option>
                    <option value="trader" {{ $role == 'trader' ? 'selected' : '' }}>{{ __('Trader') }}</option>
                    <option value="plumber" {{ $role == 'plumber' ? 'selected' : '' }}>{{ __('Plumber') }}</option>
                    <option value="distributor" {{ $role == 'distributor' ? 'selected' : '' }}>{{ __('Distributor') }}</option>
                    <option value="envoy" {{ $role == 'envoy' ? 'selected' : '' }}>{{ __('Envoy') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="PENDING" {{ $status_filter == 'PENDING' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="ACTIVE" {{ $status_filter == 'ACTIVE' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="INACTIVE" {{ $status_filter == 'INACTIVE' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    <option value="DORMANT" {{ $status_filter == 'DORMANT' ? 'selected' : '' }}>{{ __('Dormant') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="city" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ __('All Cities') }}</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ $city_filter == $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.users-information.index') }}" class="btn btn-outline-secondary w-100">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="px-4 py-3">{{ __('Name') }}</th>
                        <th class="py-3">{{ __('Role') }}</th>
                        <th class="py-3">{{ __('Phone') }}</th>
                        <th class="py-3">{{ __('Status') }}</th>
                        <th class="py-3 text-end px-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagedUsers as $user)
                        <tr>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fa fa-user text-muted"></i>
                                    </div>
                                    <span class="fw-semibold text-dark">{{ $user['name'] }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-soft-{{ 
                                    $user['role'] == 'trader' ? 'primary' : 
                                    ($user['role'] == 'plumber' ? 'success' : 
                                    ($user['role'] == 'distributor' ? 'info' : 'warning')) 
                                }} text-{{ 
                                    $user['role'] == 'trader' ? 'primary' : 
                                    ($user['role'] == 'plumber' ? 'success' : 
                                    ($user['role'] == 'distributor' ? 'info' : 'warning')) 
                                }}">
                                    {{ ucfirst($user['role']) }}
                                </span>
                            </td>
                            <td>{{ $user['phone'] }}</td>
                            <td>
                                <span class="text-{{ strtolower($user['status']) == 'approved' || strtolower($user['status']) == 'active' ? 'success' : (strtolower($user['status']) == 'pending' ? 'warning' : 'danger') }}">
                                    <i class="fa fa-circle small me-1"></i>
                                    {{ $user['status'] }}
                                </span>
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ $user['profile_route'] }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fa fa-eye me-1"></i> {{ __('View Profile') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa fa-users fa-3x mb-3 opacity-25"></i>
                                <p>{{ __('No users found matches your search or filter.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pagedUsers->hasPages())
        <div class="card-footer bg-white py-3 px-4">
            {{ $pagedUsers->links() }}
        </div>
    @endif
</div>

@push('css')
<style>
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
    
    .text-primary { color: #0d6efd !important; }
    .text-success { color: #198754 !important; }
    .text-info { color: #0dcaf0 !important; }
    .text-warning { color: #ffc107 !important; }

    .card { border: none; border-radius: 12px; }
    .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; }
    .btn-outline-primary { border-color: #0d6efd; color: #0d6efd; }
    .btn-outline-primary:hover { background-color: #0d6efd; color: #fff; }
</style>
@endpush
@endsection
