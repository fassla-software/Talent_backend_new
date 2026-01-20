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
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Total Envoys') }}</h5>
                    <h2 class="mb-0">{{ $totalEnvoys }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Total Visits') }}</h5>
                    <h2 class="mb-0">{{ $totalVisits }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.envoy.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <label for="search" class="form-label">{{ __('Search') }}</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Search by name or phone number" value="{{ request('search') }}">
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
