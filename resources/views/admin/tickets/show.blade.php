@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Ticket Details') }} - {{ $ticket['code'] }}</h4>
        <a href="{{ route('admin.ticket.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> {{ __('Back to Tickets') }}
        </a>
    </div>

    <div class="container-fluid mt-3">
        <div class="row">
            <!-- Ticket Information -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Ticket Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>{{ __('Code') }}:</strong> {{ $ticket['code'] }}
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('Status') }}:</strong>
                                <span class="badge bg-{{ $ticket['status'] === 'OPEN' ? 'warning' : ($ticket['status'] === 'IN_PROGRESS' ? 'info' : 'success') }}">
                                    {{ $ticket['status'] }}
                                </span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>{{ __('Priority') }}:</strong>
                                <span class="badge bg-{{ $ticket['priority'] === 'HIGH' ? 'danger' : ($ticket['priority'] === 'AVERAGE' ? 'warning' : 'secondary') }}">
                                    {{ $ticket['priority'] }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('Due Date') }}:</strong> 
                                {{ $ticket['due_date'] ? date('Y-m-d', strtotime($ticket['due_date'])) : 'N/A' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>{{ __('Title') }}:</strong>
                                <p>{{ $ticket['title'] }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>{{ __('Issue Description') }}:</strong>
                                <p>{{ $ticket['issue'] }}</p>
                            </div>
                        </div>
                        @if($ticket['note'])
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>{{ __('Note') }}:</strong>
                                <p>{{ $ticket['note'] }}</p>
                            </div>
                        </div>
                        @endif
                        @if($ticket['close_reason'])
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>{{ __('Close Reason') }}:</strong>
                                <p>{{ $ticket['close_reason'] }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Files Section -->
                @if($ticket['files'])
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>{{ __('Attached Files') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $files = [];
                                if ($ticket['files']) {
                                    // Extract the JSON array from the URL string
                                    $filesString = $ticket['files'];
                                    // Find the JSON array part: ["file1","file2","file3"]
                                    if (preg_match('/\[(.*?)\]/', $filesString, $matches)) {
                                        $jsonArray = '[' . $matches[1] . ']';
                                        $fileNames = json_decode($jsonArray, true);
                                        if (is_array($fileNames)) {
                                            $baseUrl = 'https://app.talentindustrial.com/plumber/uploads/';
                                            foreach ($fileNames as $fileName) {
                                                $files[] = $baseUrl . $fileName;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            @if(count($files) > 0)
                                @foreach($files as $file)
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <img src="{{ $file }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Attachment">
                                            <div class="card-body p-2">
                                                <a href="{{ $file }}" target="_blank" class="btn btn-sm btn-primary w-100">
                                                    <i class="fa-solid fa-download"></i> {{ __('View/Download') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-muted">{{ __('No files attached') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Client & Inspector Information -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Client Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ __('Name') }}:</strong> {{ $ticket['client']['name'] }}</p>
                        <p><strong>{{ __('Phone') }}:</strong> {{ $ticket['client']['phone'] }}</p>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5>{{ __('Inspector Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ __('Name') }}:</strong> {{ $ticket['inspector']['name'] }}</p>
                        <p><strong>{{ __('Phone') }}:</strong> {{ $ticket['inspector']['phone'] }}</p>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5>{{ __('Timestamps') }}</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ __('Created') }}:</strong> {{ date('Y-m-d H:i', strtotime($ticket['createdAt'])) }}</p>
                        <p><strong>{{ __('Updated') }}:</strong> {{ date('Y-m-d H:i', strtotime($ticket['updatedAt'])) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection