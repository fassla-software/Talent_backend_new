@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Inspection Request Details') }}</h4>
        <a href="{{ route('admin.inspectionRequest.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <td>{{ $request['id'] }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Status') }}</th>
                                <td>
                                    <span class="badge bg-{{ $request['status'] === 'ACCEPTED' ? 'success' : ($request['status'] === 'REJECTED' ? 'danger' : 'warning') }}">
                                        {{ $request['status'] }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('City') }}</th>
                                <td>{{ $request['city'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Area') }}</th>
                                <td>{{ $request['area'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Created At') }}</th>
                                <td>{{ isset($request['createdAt']) ? \Carbon\Carbon::parse($request['createdAt'])->format('Y-m-d H:i') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th>{{ __('Requestor') }}</th>
                                <td>{{ $request['requestor']['name'] ?? '-' }} ({{ $request['requestor']['phone'] ?? '-' }})</td>
                            </tr>
                            <tr>
                                <th>{{ __('Inspector') }}</th>
                                <td>{{ $request['inspector']['name'] ?? '-' }} ({{ $request['inspector']['phone'] ?? '-' }})</td>
                            </tr>
                            <tr>
                                <th>{{ __('Note') }}</th>
                                <td>{{ $request['note'] ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
