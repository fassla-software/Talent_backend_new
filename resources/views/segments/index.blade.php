@extends('layouts.app')

@section('content')
<div class="container">
    <h1>
        Segments
        <small class="text-muted" style="font-size: 0.9rem;">
            (Withdraw Points: {{ $withdrawPoints }})
        </small>
        
        <!-- Edit button that opens the modal -->
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editWithdrawPointsModal">
            Edit
        </button>
    </h1>

    {{-- Success and Error Messages --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Modal for editing Withdraw Points --}}
    <div class="modal fade" id="editWithdrawPointsModal" tabindex="-1" aria-labelledby="editWithdrawPointsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editWithdrawPointsModalLabel">Edit Withdraw Points</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('segments.updateWithdrawPoints') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="withdraw_points" class="form-label">Withdraw Points</label>
                            <input type="number" class="form-control" id="withdraw_points" name="withdraw_points" value="{{ old('withdraw_points', $withdrawPoints) }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Button to Open the Add Segment Modal --}}
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addSegmentModal">
        Add Segment
    </button>

    {{-- Add Segment Modal --}}
    <div class="modal fade" id="addSegmentModal" tabindex="-1" aria-labelledby="addSegmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSegmentModalLabel">Add Segment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('segments.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" name="description" id="description" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="minPoints" class="form-label">Minimum Points</label>
                            <input type="number" name="minPoints" id="minPoints" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="maxPoints" class="form-label">Maximum Points</label>
                            <input type="number" name="maxPoints" id="maxPoints" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="pointsValue" class="form-label">Points Value</label>
                            <input type="number" name="pointsValue" id="pointsValue" class="form-control" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Add Segment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Display Segments --}}
    @if (!empty($segments))
        <div class="row">
            @foreach ($segments as $segment)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $segment['description'] }}</h5>
                            <p class="card-text">
                                <strong>Points Range:</strong> {{ $segment['minPoints'] }} - {{ $segment['maxPoints'] }}<br>
                                <strong>Points Value:</strong> ${{ $segment['pointsValue'] }}<br>
                                <strong>Created At:</strong> {{ \Carbon\Carbon::parse($segment['createdAt'])->format('Y-m-d H:i:s') }}<br>
                                <strong>Updated At:</strong> {{ \Carbon\Carbon::parse($segment['updatedAt'])->format('Y-m-d H:i:s') }}
                            </p>
                            {{-- Edit Button --}}
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editSegmentModal{{ $segment['id'] }}">
                                Edit
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Edit Segment Modal --}}
                <div class="modal fade" id="editSegmentModal{{ $segment['id'] }}" tabindex="-1" aria-labelledby="editSegmentModalLabel{{ $segment['id'] }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editSegmentModalLabel{{ $segment['id'] }}">Edit Segment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('segments.update', $segment['id']) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="description{{ $segment['id'] }}" class="form-label">Description</label>
                                        <input type="text" name="description" id="description{{ $segment['id'] }}" class="form-control" value="{{ $segment['description'] }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="minPoints{{ $segment['id'] }}" class="form-label">Minimum Points</label>
                                        <input type="number" name="minPoints" id="minPoints{{ $segment['id'] }}" class="form-control" value="{{ $segment['minPoints'] }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="maxPoints{{ $segment['id'] }}" class="form-label">Maximum Points</label>
                                        <input type="number" name="maxPoints" id="maxPoints{{ $segment['id'] }}" class="form-control" value="{{ $segment['maxPoints'] }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pointsValue{{ $segment['id'] }}" class="form-label">Points Value</label>
                                        <input type="number" name="pointsValue" id="pointsValue{{ $segment['id'] }}" class="form-control" step="0.01" value="{{ $segment['pointsValue'] }}" required>
                                    </div>
                                    <button type="submit" class="btn btn-success">Update Segment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
