@extends('layouts.app')

@section('title', 'Traders')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Traders Management</h1>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Traders List</h6>
                    <form method="GET" action="{{ route('admin.traders') }}" class="d-flex flex-wrap gap-2 mt-3">
                        <select name="status" class="form-control w-auto">
                            <option value="">All Status</option>
                            <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                            <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Approved</option>
                            <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                        </select>

                        <input type="text" name="nationality_id" class="form-control w-auto"
                               placeholder="Nationality ID" value="{{ request('nationality_id') }}">

                        <input type="text" name="name" class="form-control w-auto"
                               placeholder="Name" value="{{ request('name') }}">

                        <select name="city" class="form-control w-auto">
                            <option value="">All Cities</option>
                            @foreach ($allCities as $city)
                            <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                            @endforeach
                        </select>

                        <select name="created_month" class="form-control w-auto">
                            <option value="">All Months</option>
                            @foreach ($availableMonths as $month)
                            <option value="{{ $month }}" {{ request('created_month') === $month ? 'selected' : '' }}>
                                {{ date('F Y', strtotime($month . '-01')) }}
                            </option>
                            @endforeach
                        </select>

                        <input type="text" name="phone" class="form-control w-auto"
                               placeholder="Phone" value="{{ request('phone') }}">

                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.traders') }}" class="btn btn-secondary">Clear</a>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($traders as $trader)
                                    <tr>
                                        <td>{{ $trader->id }}</td>
                                        <td>{{ $trader->user->name ?? 'No User' }}</td>
                                        <td>{{ $trader->user->phone ?? 'N/A' }}</td>
                                        <td>{{ $trader->city ?? 'N/A' }}</td>
                                        <td>{{ $trader->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.traders.show', $trader->id) }}" class="btn btn-sm btn-info">
                                               <i class="bi bi-eye"></i> View
                                            </a>

                                            <button data-toggle="modal" data-target="#traderModalEdit{{ $trader->user_id }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>

                                            @if ($trader->user->status === 'PENDING')
                                            <form action="{{ route('admin.traders.approve', $trader->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to approve this trader?');">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>

                                            <form action="{{ route('admin.traders.reject', $trader->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to reject this trader?');">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No traders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $traders->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

            @forelse($traders as $trader)
            <!-- Modal for each trader -->
            <div class="modal fade" id="traderModal{{ $trader->id }}" tabindex="-1" role="dialog" aria-labelledby="traderModalLabel{{ $trader->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="traderModalLabel{{ $trader->id }}">Trader Details (ID: {{ $trader->id }})</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Displaying Trader Details in the Modal -->
                            <p><strong>Name:</strong> {{ $trader->user->name }}</p>
                            <p><strong>City:</strong> {{ $trader->city }}</p>
                            <p><strong>Area:</strong> {{ $trader->area }}</p>
                            <p><strong>Nationality:</strong> {{ $trader->nationality_id ?? 'No Nationality' }}</p>
                            <p><strong>Phone:</strong> {{ $trader->user->phone ?? 'No Phone' }}</p>
                            <p><strong>Verified?</strong>
                                {{ isset($trader->is_verified) && $trader->is_verified ? 'Yes' : 'No' }}
                            </p>
                            <p><strong>Points:</strong> {{ $trader->points }}</p>
                            <p><strong>Created At:</strong> {{ \Carbon\Carbon::parse($trader->created_at)->format('Y-m-d H:i:s') }}</p>
                            <p><strong>Updated At:</strong> {{ \Carbon\Carbon::parse($trader->updated_at)->format('Y-m-d H:i:s') }}</p>

                            <!-- Displaying images in the modal -->
                            <p><strong>Nationality Image 1:</strong></p>
                            @if($trader->nationality_image1)
                            <img src="https://app.talentindustrial.com/plumber/uploads/{{ $trader->nationality_image1 }}" alt="Nationality Image 1" class="img-fluid mb-2" style="max-height: 200px;">
                            @else
                            <p>No Image Available</p>
                            @endif

                            <p><strong>Nationality Image 2:</strong></p>
                            @if($trader->nationality_image2)
                            <img src="https://app.talentindustrial.com/plumber/uploads/{{ $trader->nationality_image2 }}" alt="Nationality Image 2" class="img-fluid mb-2" style="max-height: 200px;">
                            @else
                            <p>No Image Available</p>
                            @endif

                            <p><strong>Trader Image:</strong></p>
                            @if($trader->image)
                            <img src="https://app.talentindustrial.com/plumber/uploads/{{ $trader->image }}" alt="Trader Image" class="img-fluid mb-2" style="max-height: 200px;">
                            @else
                            <p>No Image Available</p>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal for each trader -->
            <div class="modal fade" id="traderModalEdit{{ $trader->user_id }}" tabindex="-1" role="dialog" aria-labelledby="traderModalEditLabel{{ $trader->user_id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="traderModalEditLabel{{ $trader->user_id }}">Edit Trader Details (ID: {{ $trader->user_id }})</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="save-changes-form" method="post" action="{{ route('admin.traders.update', $trader->user_id) }}">
                                @csrf
                                @method('patch')

                                <!-- Name Field -->
                                <label>Name</label>
                                <div class="input-group mb-3">
                                    <input type="text" name="name" value="{{ $trader->user->name }}" class="form-control" placeholder="Name" aria-label="Name">
                                </div>

                                <!-- Phone Field -->
                                <label>Phone</label>
                                <div class="input-group mb-3">
                                    <input type="text" name="phone" value="{{ $trader->user->phone }}" class="form-control" placeholder="Phone" aria-label="Phone">
                                </div>

                                <!-- nationality_id -->
                                <label>National ID</label>
                                <div class="input-group mb-3">
                                    <input
                                        type="text"
                                        name="nationality_id"
                                        value="{{ $trader->nationality_id }}"
                                        class="form-control"
                                        placeholder="nationality id"
                                        aria-label="nationality_id"
                                        pattern="\d{14}"
                                        maxlength="14"
                                        minlength="14"
                                        inputmode="numeric"
                                        title="Nationality ID must be exactly 14 digits">
                                </div>
                                <!-- points -->
                                <label>Points</label>
                                <div class="input-group mb-3">
                                    <input type="number" min="0" name="points" value="{{ $trader->points }}" class="form-control" placeholder="points" aria-label="points">
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            @endforelse
        </div>
    </div>
</div>

@endsection

<style>
    /* Modal Styles */
    .modal-content {
        background-color: #ffffff;
        border: 1px solid #28a745;
        border-radius: 8px;
        padding: 10px;
    }

    .modal-header {
        background-color: #28a745;
        color: white;
        border-bottom: 1px solid #28a745;
        font-size: 1.2rem;
        padding: 10px;
    }

    .modal-footer {
        border-top: 1px solid #28a745;
        padding: 10px;
    }

    .modal-title {
        font-weight: bold;
        font-size: 1.1rem;
    }

    .modal-body p {
        font-size: 1rem;
        margin: 10px 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f1f1;
    }

    .modal-body p:last-child {
        border-bottom: none;
    }

    .btn-info {
        background-color: #28a745;
        border-color: #28a745;
        padding: 8px 15px;
        font-size: 0.9rem;
        border-radius: 5px;
    }

    .btn-info:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        padding: 8px 15px;
        font-size: 0.9rem;
        border-radius: 5px;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    .modal-body img {
        max-width: 100%;
        height: auto;
        margin-top: 5px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .modal-dialog {
        max-width: 60%;
        width: auto;
    }

    .modal-content {
        background-color: #ffffff;
        border: 1px solid #28a745;
        border-radius: 8px;
        padding: 8px;
    }

    .modal-header {
        background-color: #28a745;
        color: white;
        border-bottom: 1px solid #28a745;
        font-size: 1rem;
        padding: 8px;
    }

    .modal-footer {
        border-top: 1px solid #28a745;
        padding: 8px;
    }

    .modal-title {
        font-weight: bold;
        font-size: 1rem;
    }

    .modal-body p {
        font-size: 0.9rem;
        margin: 8px 0;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f1f1;
    }

    .modal-body p:last-child {
        border-bottom: none;
    }

    .btn-info {
        background-color: #28a745;
        border-color: #28a745;
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 4px;
    }

    .btn-info:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 4px;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    .modal-body img {
        max-width: 100%;
        height: auto;
        margin-top: 5px;
        border-radius: 5px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

    .modal-title {
        color: white !important;
    }
</style>