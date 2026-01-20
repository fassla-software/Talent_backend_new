@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Visit Details') }}</h4>
        <a href="{{ route('admin.inspectionVisit.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> {{ __('Back to List') }}
        </a>
    </div>

    <div class="container-fluid mt-3">
        <div class="row">
            <!-- Visit Information -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>{{ __('Visit Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>{{ __('Status') }}:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $visit['status'] === 'APPROVED' ? 'success' : ($visit['status'] === 'REJECTED' ? 'danger' : 'warning') }}">
                                        {{ $visit['status'] }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Check In') }}:</strong></td>
                                <td>{{ $visit['check_in_at'] ? \Carbon\Carbon::parse($visit['check_in_at'])->format('Y-m-d H:i:s') : '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Check Out') }}:</strong></td>
                                <td>{{ $visit['check_out_at'] ? \Carbon\Carbon::parse($visit['check_out_at'])->format('Y-m-d H:i:s') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Inspector Information -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>{{ __('Inspector Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>{{ __('Name') }}:</strong></td>
                                <td>{{ $visit['inspector']['name'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Phone') }}:</strong></td>
                                <td>{{ $visit['inspector']['phone'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Email') }}:</strong></td>
                                <td>{{ $visit['inspector']['email'] ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>{{ __('Location Information') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __('Location Status') }}:</strong>
                            @if(isset($visit['location_status']))
                                @if($visit['location_status'] === 'inside')
                                    <span class="badge bg-success">
                                        <i class="fa-solid fa-location-dot"></i> Inside (Within 100m)
                                    </span>
                                @elseif($visit['location_status'] === 'outside')
                                    <span class="badge bg-warning text-dark">
                                        <i class="fa-solid fa-location-crosshairs"></i> Outside (Beyond 100m)
                                    </span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">N/A</span>
                            @endif
                        </p>
                    </div>
                    @if(isset($visit['distance_meters']))
                    <div class="col-md-6">
                        <p><strong>{{ __('Distance from Location') }}:</strong> {{ number_format($visit['distance_meters'], 2) }}m</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if(isset($visit['visitReport']))
        <!-- Visit Report -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>{{ __('Visit Report') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>{{ __('Customer Information') }}</h6>
                        <table class="table table-sm">
                            <tr><td><strong>{{ __('Customer Name') }}:</strong></td><td>{{ $visit['visitReport']['customer_name'] ?? '-' }}</td></tr>
                            <tr><td><strong>{{ __('Company') }}:</strong></td><td>{{ $visit['visitReport']['company_name'] ?? '-' }}</td></tr>
                            <tr><td><strong>{{ __('Phone') }}:</strong></td><td>{{ $visit['visitReport']['phone'] ?? '-' }}</td></tr>
                            <tr><td><strong>{{ __('Email') }}:</strong></td><td>{{ $visit['visitReport']['email'] ?? '-' }}</td></tr>
                            <tr><td><strong>{{ __('Location') }}:</strong></td><td>{{ $visit['visitReport']['location'] ?? '-' }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>{{ __('Visit Details') }}</h6>
                        <table class="table table-sm">
                            <tr><td><strong>{{ __('Client Type') }}:</strong></td><td>{{ $visit['visitReport']['client_type'] ?? '-' }}</td></tr>
                            <tr><td><strong>{{ __('Visit Type') }}:</strong></td><td>{{ $visit['visitReport']['visit_type'] ?? '-' }}</td></tr>
                            <tr><td><strong>{{ __('Visit Result') }}:</strong></td><td>{{ $visit['visitReport']['visit_result'] ?? '-' }}</td></tr>
                            <tr><td><strong>{{ __('Interest Level') }}:</strong></td><td>{{ $visit['visitReport']['interest_level'] ?? '-' }}</td></tr>
                            <tr><td><strong>{{ __('Sales Value') }}:</strong></td><td>{{ $visit['visitReport']['sales_value'] ?? '-' }}</td></tr>
                        </table>
                    </div>
                </div>
                
                @if($visit['visitReport']['additional_notes'])
                <div class="mt-3">
                    <h6>{{ __('Additional Notes') }}</h6>
                    <p>{{ $visit['visitReport']['additional_notes'] }}</p>
                </div>
                @endif

                @if(isset($visit['visitReport']['photos']) && !empty($visit['visitReport']['photos']))
                <div class="mt-3">
                    <h6>{{ __('Photos') }}</h6>
                    <div class="row">
                        @php
                            $photosString = $visit['visitReport']['photos'];
                            $photos = [];
                            
                            if (is_string($photosString)) {
                                // Extract base URL and array part
                                if (strpos($photosString, 'uploads/[') !== false) {
                                    $baseUrl = 'https://app.talentindustrial.com/plumber/uploads/';
                                    $arrayPart = substr($photosString, strpos($photosString, '['));
                                    $imageNames = json_decode($arrayPart, true);
                                    
                                    if (is_array($imageNames)) {
                                        foreach ($imageNames as $imageName) {
                                            $photos[] = $baseUrl . $imageName;
                                        }
                                    }
                                } else {
                                    // Single photo URL
                                    $photos = [$photosString];
                                }
                            } elseif (is_array($photosString)) {
                                $photos = $photosString;
                            }
                        @endphp
                        @foreach($photos as $photo)
                            <div class="col-md-3 mb-2">
                                <img src="{{ $photo }}" class="img-fluid rounded" alt="Visit Photo">
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Status Update Actions -->
        @if($visit['status'] === 'PENDING')
        <div class="card">
            <div class="card-header">
                <h5>{{ __('Update Status') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="updateStatus('APPROVED')">
                        <i class="fa-solid fa-check"></i> {{ __('Approve Visit') }}
                    </button>
                    <button type="button" class="btn btn-danger" onclick="updateStatus('REJECTED')">
                        <i class="fa-solid fa-times"></i> {{ __('Reject Visit') }}
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Status Update Form -->
    <form id="statusForm" method="POST" action="{{ route('admin.inspectionVisit.updateStatus', $visit['id']) }}" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" id="statusInput">
    </form>
@endsection

@push('scripts')
<script>
    function updateStatus(status) {
        if (confirm(`Are you sure you want to ${status.toLowerCase()} this visit?`)) {
            document.getElementById('statusInput').value = status;
            document.getElementById('statusForm').submit();
        }
    }
</script>
@endpush