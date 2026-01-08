@extends('layouts.app')

@section('title', 'Distributors')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Distributors Management</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDistributorModal">
                    <i class="fas fa-plus"></i> Add New Distributor
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distributors List</h6>
                    <form method="GET" action="{{ route('admin.distributor.index') }}" class="d-flex flex-wrap gap-2 mt-3">

    <!-- Search -->
    <input type="text" name="search" class="form-control w-auto"
           placeholder="Search by phone, name, or city"
           value="{{ request('search') }}">

    <!-- From Date -->
    <input type="date" name="from_date" class="form-control w-auto"
           value="{{ request('from_date') }}">

    <!-- To Date -->
    <input type="date" name="to_date" class="form-control w-auto"
           value="{{ request('to_date') }}">

    <!-- Buttons -->
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="{{ route('admin.distributor.index') }}" class="btn btn-secondary">Clear</a>

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
                                    <th>State</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($distributors as $distributor)
                                    <tr>
                                        <td>{{ $distributor->id }}</td>
                                        <td>{{ $distributor->name }}</td>
                                        <td>{{ $distributor->phone }}</td>
                                        <td>{{ $distributor->city ?? 'N/A' }}</td>
                                        <td>{{ $distributor->state ?? 'N/A' }}</td>
                                        <td>{{ $distributor->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.distributor.show', $distributor->id) }}" class="btn btn-sm btn-info">
                                               <i class="bi bi-eye"></i> View
                                            </a>


                                            <a href="{{ route('admin.distributor.edit', $distributor->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.distributor.destroy', $distributor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this distributor?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">
        <i class="fas fa-trash"></i> Delete
    </button>
</form>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No distributors found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $distributors->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Distributor Modal -->
<div class="modal fade" id="addDistributorModal" tabindex="-1" aria-labelledby="addDistributorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDistributorModalLabel">Add New Distributor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addDistributorForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                        <select class="form-control" id="city" name="city" required>
                         <option value="">Select City</option>
        @foreach ($allCities as $city)
            <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>
                {{ $city }}
            </option>
        @endforeach
</select>

                    <div class="mb-3">
                        <label for="state" class="form-label">State</label>
                        <input type="text" class="form-control" id="state" name="state">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Distributor</button>
                </div>
            </form>
        </div>
    </div>
</div>
                        
                        
<script>
// Handle add distributor form
document.getElementById('addDistributorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('/admin/distributors', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addDistributorModal')).hide();
            location.reload();
        } else {
            alert('Error adding distributor');
        }
    });
});
</script>                        
@endsection
