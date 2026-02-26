@extends('layouts.app')

@section('content')
<div class="container-fluid mt-5">
    <div class="row mb-5 align-items-center">
        <div class="col-md-5">
            <h1 class="m-0">Withdraw Requests</h1>
        </div>
        <div class="col-md-7 d-flex justify-content-end gap-3 align-items-center">
            @hasPermission('plumber.withdrawal.edit')
                <form action="{{ route('withdraw.upload') }}" method="POST" enctype="multipart/form-data" class="d-flex m-0">
                    @csrf
                    <div class="input-group" style="height: 45px;">
                        <input type="file" name="file" class="form-control h-100" accept=".csv, .xlsx" required>
                        <button type="submit" class="btn btn-primary px-4 h-100">Upload Excel</button>
                    </div>
                </form>
            @endhasPermission
            <a href="{{ route('withdraw.download') }}" class="btn btn-success d-flex align-items-center px-4 m-0 shadow-sm" style="height: 45px;">
                <i class="fa fa-file-excel me-2"></i> Download Excel
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('withdraw.index') }}" class="card card-body mb-4 shadow-sm">
        <div class="row g-3">
            <!-- Filter by Status -->
            <div class="col-md-2">
                <label for="status" class="form-label small fw-bold">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Filter by Transaction Type -->
            <div class="col-md-2">
                <label for="transaction_type" class="form-label small fw-bold">Transaction Type</label>
                <select name="transaction_type" id="transaction_type" class="form-control">
                    <option value="">All Types</option>
                    <option value="wallet" {{ request('transaction_type') === 'wallet' ? 'selected' : '' }}>Wallet</option>
                    <option value="bank" {{ request('transaction_type') === 'bank' ? 'selected' : '' }}>Bank</option>
                    <option value="meeza" {{ request('transaction_type') === 'meeza' ? 'selected' : '' }}>Meeza</option>
                </select>
            </div>

            <!-- Filter by Name -->
            <div class="col-md-3">
                <label for="name" class="form-label small fw-bold">Name</label>
                <input type="text" name="name" id="name" value="{{ request('name') }}" class="form-control" placeholder="Search by name">
            </div>

            <!-- Filter by Phone -->
            <div class="col-md-3">
                <label for="phone" class="form-label small fw-bold">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ request('phone') }}" placeholder="Search by phone">
            </div>

            <!-- Action Buttons -->
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('withdraw.index') }}" class="btn btn-secondary w-100">Reset</a>
            </div>
        </div>
    </form>

    <!-- Quick Filters -->
    <div class="mb-4 no-print">
        <div class="d-flex flex-wrap align-items-center gap-4">
            <!-- Status Quick Filters -->
            <div class="d-flex align-items-center gap-2">
                <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Status:</span>
                <div class="btn-group shadow-sm" role="group">
                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" 
                       class="btn btn-sm {{ !request('status') ? 'btn-primary active' : 'btn-light border' }}">All</a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" 
                       class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning active' : 'btn-light border' }}">Pending</a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}" 
                       class="btn btn-sm {{ request('status') === 'approved' ? 'btn-success active' : 'btn-light border' }}">Approved</a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}" 
                       class="btn btn-sm {{ request('status') === 'rejected' ? 'btn-danger active' : 'btn-light border' }}">Rejected</a>
                </div>
            </div>

            <!-- Transaction Type Quick Filters -->
            <div class="d-flex align-items-center gap-2">
                <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Type:</span>
                <div class="btn-group shadow-sm" role="group">
                    <a href="{{ request()->fullUrlWithQuery(['transaction_type' => null]) }}" 
                       class="btn btn-sm {{ !request('transaction_type') ? 'btn-secondary active' : 'btn-light border' }}">All</a>
                    <a href="{{ request()->fullUrlWithQuery(['transaction_type' => 'wallet']) }}" 
                       class="btn btn-sm {{ request('transaction_type') === 'wallet' ? 'btn-info active' : 'btn-light border' }}">Wallet</a>
                    <a href="{{ request()->fullUrlWithQuery(['transaction_type' => 'bank']) }}" 
                       class="btn btn-sm {{ request('transaction_type') === 'bank' ? 'btn-dark active' : 'btn-light border' }}">Bank</a>
                    <a href="{{ request()->fullUrlWithQuery(['transaction_type' => 'meeza']) }}" 
                       class="btn btn-sm {{ request('transaction_type') === 'meeza' ? 'btn-primary active' : 'btn-light border' }}">Meeza</a>
                </div>
            <!-- Date Quick Filters -->
            <div class="d-flex align-items-center gap-2">
                <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Date:</span>
                <div class="btn-group shadow-sm" role="group">
                    <a href="{{ request()->fullUrlWithQuery(['date_filter' => 'today', 'from_date' => null, 'to_date' => null, 'page' => null]) }}" 
                       class="btn btn-sm {{ request('date_filter', 'today') === 'today' ? 'btn-primary active' : 'btn-light border' }}">Today</a>
                    <a href="{{ request()->fullUrlWithQuery(['date_filter' => 'this_week', 'from_date' => null, 'to_date' => null, 'page' => null]) }}" 
                       class="btn btn-sm {{ request('date_filter') === 'this_week' ? 'btn-primary active' : 'btn-light border' }}">This Week</a>
                    <a href="{{ request()->fullUrlWithQuery(['date_filter' => 'this_month', 'from_date' => null, 'to_date' => null, 'page' => null]) }}" 
                       class="btn btn-sm {{ request('date_filter') === 'this_month' ? 'btn-primary active' : 'btn-light border' }}">This Month</a>
                    <a href="{{ request()->fullUrlWithQuery(['date_filter' => 'this_year', 'from_date' => null, 'to_date' => null, 'page' => null]) }}" 
                       class="btn btn-sm {{ request('date_filter') === 'this_year' ? 'btn-primary active' : 'btn-light border' }}">This Year</a>
                    <button type="button" id="custom-date-btn" onclick="toggleCustomDate()" 
                            class="btn btn-sm {{ request('date_filter') === 'custom' ? 'btn-primary active' : 'btn-light border' }}">Custom</button>
                </div>
            </div>
        </div>

        <!-- Custom Date Range Form -->
        <div id="custom-date-container" class="mt-3 {{ request('date_filter') === 'custom' ? '' : 'd-none' }}">
            <form method="GET" action="{{ route('withdraw.index') }}" class="d-flex align-items-end gap-2 p-3 bg-light border rounded shadow-sm">
                @foreach(request()->except(['date_filter', 'from_date', 'to_date', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <input type="hidden" name="date_filter" value="custom">
                <div>
                    <label class="form-label small fw-bold">From</label>
                    <input type="date" name="from_date" value="{{ request('from_date', now()->format('Y-m-d')) }}" class="form-control form-control-sm" required>
                </div>
                <div>
                    <label class="form-label small fw-bold">To</label>
                    <input type="date" name="to_date" value="{{ request('to_date', now()->format('Y-m-d')) }}" class="form-control form-control-sm" required>
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">Apply Range</button>
                <button type="button" onclick="toggleCustomDate(false)" class="btn btn-outline-secondary btn-sm shadow-sm">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Skeleton Loader -->
    <div id="skeleton-loader">
        <div class="row mb-4 mt-3">
            @for($i = 0; $i < 4; $i++)
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="skeleton skeleton-title"></div>
                        <div class="skeleton skeleton-text" style="width: 40%"></div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
        <div class="card shadow-sm p-4">
            @for($i = 0; $i < 6; $i++)
                <div class="skeleton skeleton-row"></div>
            @endfor
        </div>
    </div>

    <div id="content-real">
    <!-- Stats Cards -->
    <div class="row mb-4 mt-3">
        <!-- Pending Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" class="text-decoration-none card-filter-link">
                <div class="card border-left-warning shadow h-100 py-2 transition-hover" style="border-left: 0.25rem solid #f6c23e !important;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.8rem;">Pending</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Approved Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}" class="text-decoration-none card-filter-link">
                <div class="card border-left-success shadow h-100 py-2 transition-hover" style="border-left: 0.25rem solid #1cc88a !important;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="font-size: 0.8rem;">Approved</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Rejected Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}" class="text-decoration-none card-filter-link">
                <div class="card border-left-danger shadow h-100 py-2 transition-hover" style="border-left: 0.25rem solid #e74a3b !important;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1" style="font-size: 0.8rem;">Rejected</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejected'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Amount Card (Not Filterable by click but styled consistently) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2" style="border-left: 0.25rem solid #4e73df !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="font-size: 0.8rem;">Total Approved Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_approved_amount'], 2) }} EGP</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .transition-hover {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            cursor: pointer;
        }
        .transition-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .card-filter-link:hover .text-gray-800 {
            color: #4e73df !important;
        }

        /* Skeleton Loading Styles */
        .skeleton {
            background: #eee;
            background: linear-gradient(110deg, #ececec 8%, #f5f5f5 18%, #ececec 33%);
            border-radius: 5px;
            background-size: 200% 100%;
            animation: 1.5s shine linear infinite;
        }
        @keyframes shine {
            to {
                background-position-x: -200%;
            }
        }
        .skeleton-text { height: 15px; width: 100%; margin-bottom: 10px; }
        .skeleton-title { height: 20px; width: 60%; margin-bottom: 15px; }
        .skeleton-card { height: 100px; }
        .skeleton-row { height: 40px; margin-bottom: 5px; }
        
        #content-real { opacity: 0; transition: opacity 0.4s ease-in-out; }
        #content-real.loaded { opacity: 1; }
        #skeleton-loader { display: block; }
        #skeleton-loader.hidden { display: none; }
    </style>
    <script>
        function toggleCustomDate(show = true) {
            const container = document.getElementById('custom-date-container');
            const btn = document.getElementById('custom-date-btn');
            
            if (show === false || !container.classList.contains('d-none')) {
                container.classList.add('d-none');
                btn.classList.remove('btn-primary', 'active');
                btn.classList.add('btn-light', 'border');
            } else {
                container.classList.remove('d-none');
                btn.classList.add('btn-primary', 'active');
                btn.classList.remove('btn-light', 'border');
            }
        }
    </script>
    <!-- Withdraw Requests Table -->
    <table class="table table-bordered" style="position: relative; z-index: 1;">
        <thead>
        <tr>
            <th>ID</th>
            <th>Requestor Name</th>
            <th>Phone</th>
            <th>Amount</th>
            <th>Transaction Type</th>
    		<th>Payment Identifier</th>
            <th>Request Date</th>
            <th>Status</th>
            <th>Rejection Reason</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($data ?? [] as $withdraw)
            <tr>
                <td>{{ $withdraw['id'] }}</td>
                <td>{{ $withdraw['plumber']['user']['name'] ?? 'Unknown' }}</td>
                <td>{{ $withdraw['plumber']['user']['phone'] ?? 'N/A' }}</td>
                <td>{{ $withdraw['amount'] }}</td>
                <td>{{ ucfirst($withdraw['transaction_type']) }}</td>
 			    <td>{{ $withdraw['payment_identifier'] ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($withdraw['request_date'])->format('Y-m-d H:i:s') }}</td>
                <td>
                    @if($withdraw['status'] === 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($withdraw['status'] === 'approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif($withdraw['status'] === 'rejected')
                        <span class="badge bg-danger">Rejected</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst($withdraw['status']) }}</span>
                    @endif
                </td>
                <td>{{ $withdraw['rejection_reason'] ?? 'N/A' }}</td>
                <td>
    
    @hasPermission('plumber.withdrawal.edit')
    @if($withdraw['status'] === 'pending')
        <div class="dropdown" style="display: inline; position: relative;">
            <button 
                class="btn btn-secondary btn-sm dropdown-toggle" 
                type="button"
                id="dropdownMenuButton{{ $withdraw['id'] }}" 
                data-bs-toggle="dropdown" 
                aria-expanded="false"
                style="z-index: 1050;">
                Status
            </button>
            <ul 
                class="dropdown-menu" 
                aria-labelledby="dropdownMenuButton{{ $withdraw['id'] }}" 
                style="z-index: 1050; position: absolute;">
                <li>
                    <form action="{{ route('withdraw.updateStatus', $withdraw['id']) }}" method="POST" id="status-form-{{ $withdraw['id'] }}-approved">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <a class="dropdown-item" href="javascript:void(0)" onclick="document.getElementById('status-form-{{ $withdraw['id'] }}-approved').submit();">Approved</a>
                    </form>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0)" 
                       onclick="openRejectionModal('{{ route('withdraw.updateStatus', $withdraw['id']) }}')">
                        Rejected
                    </a>
                </li>
            </ul>
        </div>
    @endif
    @endhasPermission

    
    
                   <div class="d-inline-block">
    <a style="font-size: 1rem;" class="btn btn-warning btn-sm view-logs-btn" 
       data-id="{{ $withdraw['plumber']['user_id'] }}" 
       data-user="{{ $withdraw['plumber']['user'] }}">
        <i class="fa fa-eye"></i>
    </a>

    <a style="font-size: 1rem;" class="btn btn-info btn-sm" 
       href="{{ route('withdraw.downloadUser', ['userId' => $withdraw['plumber']['user_id']]) }}">
        <i class="fa fa-file-download"></i>
    </a>

    <form method="POST" class="delete-form-withdraw" style="display: none" 
          data-route="{{ route('withdraw.destroy', $withdraw['id']) }}">
        @method('DELETE')
        @csrf
        <button style="font-size: 1rem;" type="submit" class="btn btn-danger btn-sm">
            <i class="fa fa-trash"></i>
        </button>
    </form>
</div>

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">No withdraw requests available.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center mt-4 mb-5">
        <div>
            <!-- Pagination (if needed) -->
            {{ $data->links() }}
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Rows per page:</span>
            <select name="per_page" id="per_page_select" class="form-select form-select-sm" style="width: auto;" onchange="updatePerPage(this.value)">
                @foreach([10, 25, 50, 100] as $count)
                    <option value="{{ $count }}" {{ request('per_page') == $count ? 'selected' : '' }}>{{ $count }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectionForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectionModalLabel">Reject Withdraw Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" required placeholder="Enter the reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openRejectionModal(actionUrl) {
        document.getElementById('rejectionForm').action = actionUrl;
        var myModal = new bootstrap.Modal(document.getElementById('rejectionModal'));
        myModal.show();
    }

    function updatePerPage(perPage) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page'); // Reset to page 1 when changing per_page
        window.location.href = url.toString();
    }

    // Hide skeleton and show real content when page is ready
    window.addEventListener('load', function() {
        setTimeout(() => { // Small timeout for visual smoothness
            document.getElementById('skeleton-loader').classList.add('hidden');
            document.getElementById('content-real').classList.add('loaded');
        }, 300);
    });
</script>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const viewLogsBtns = document.querySelectorAll('.view-logs-btn');

    viewLogsBtns.forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.dataset.id; // Get the user ID from the data-id attribute
            const user = JSON.parse(this.dataset.user);

            // Fetch data via AJAX
            fetch(`{{ url('/withdraws/logs/') }}/${userId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const baseURL = 'https://app.talentindustrial.com/plumber/uploads/';
                    
                    // Build table rows dynamically
                    const tableRows = data.map(request => {
                        const imageURL = request.image 
                            ? `${baseURL}${request.image}` 
                            : null;

                        return `
                        <tr>
                            <td>${request.id}</td>
                            <td>${user?.name || 'N/A'} ${request.plumber?.last_name || ''}</td>
                            <td>${user?.phone || 'N/A'}</td>
                            <td>${request.amount}</td>
                            <td>${request.transaction_type}</td>
                            <td>${request.request_date}</td>
                            <td>${request.status}</td>
                            <td>${request.rejection_reason || 'N/A'}</td>
                            <td>
                                ${imageURL 
                                    ? `<img 
                                        src="${imageURL}" 
                                        alt="Image" 
                                        class="request-image" 
                                        style="width: 50px; height: 50px; cursor: pointer;" 
                                      />`
                                    : `<span>No Image</span>`
                                }
                            </td>
                        </tr>`;
                    }).join('');

                    // Build the full table HTML
                    const tableContent = `
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Requestor Name</th>
                                    <th>Phone</th>
                                    <th>Amount</th>
                                    <th>Transaction Type</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th>Rejection Reason</th>
                                    <th>Image</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    `;

                    // Open a new window and insert the table content
                    const newWindow = window.open('', '', 'width=800,height=600');
                    newWindow.document.write(`
                        <html>
                            <head>
                                <title>Withdrawal History</title>
                                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                                <style>
                                    body {
                                        font-family: 'Arial', sans-serif;
                                        background-color: #f8f9fa;
                                        color: #343a40;
                                        display: flex;
                                        justify-content: center; /* Center horizontally */
                                        align-items: center; /* Center vertically */
                                        min-height: 100vh; /* Take full height of the viewport */
                                        margin: 0;
                                    }

                                    h3 {
                                        color: #347928;
                                        margin-bottom: 30px;
                                        text-align: center;
                                        font-size: 2.5rem;
                                        font-weight: 600;
                                    }

                                    .container {
                                        background-color: white;
                                        border-radius: 8px;
                                        padding: 30px;
                                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                                        width: 100%;
                                        max-width: 1200px; /* Optional: Limit the maximum width of the container */
                                        overflow: visible; /* Prevent clipping */
                                    }

                                    table {
                                        width: 100%;
                                        margin-top: 20px;
                                        border-collapse: collapse;
                                        border: 1px solid #dee2e6;
                                        border-radius: 8px;
                                        overflow: visible;
                                        position: relative; /* Create a new stacking context for the table */
                                        z-index: 1; /* Ensure it's lower than the dropdown */
                                    }

                                    table th, table td {
                                        padding: 15px;
                                        text-align: left;
                                        font-size: 1.1rem;
                                    }

                                    table th {
                                        background: linear-gradient(90deg, #347928, #347928);
                                        color: white;
                                        font-weight: 700;
                                    }

                                    table tr:nth-child(even) {
                                        background-color: #f8f9fa;
                                    }

                                    table tr:hover {
                                        background-color: #e9ecef;
                                    }

                                    .btn-download {
                                        display: inline-block;
                                        margin-top: 20px;
                                        padding: 10px 20px;
                                        background-color: #28a745;
                                        color: white;
                                        border: none;
                                        border-radius: 5px;
                                        font-size: 1rem;
                                        cursor: pointer;
                                    }

                                    .btn-download:hover {
                                        background-color: #218838;
                                    }

                                    .btn-download:focus {
                                        outline: none;
                                    }
                                                    
                                    .dropdown-menu {
                                        position: absolute;
                                        z-index: 1050; /* Bootstrap default z-index for dropdowns */
                                    }
                                </style>
                            </head>
                            <body>
                                <div class="container">
                                    <h3>Withdrawal History</h3>
                                    ${tableContent}
                                </div>
                                <script>
                                    // Add click event to images in the new window
                                    document.querySelectorAll('.request-image').forEach(img => {
                                        img.addEventListener('click', function () {
                                            const popup = window.open('', '', 'width=600,height=600');
                                            popup.document.write('<img src="' + this.src + '" style="width:100%;height:100%;" />');
                                        });
                                    });
                                <\/script>
                            </body>
                        </html>
                    `);
                    newWindow.document.close();
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    alert('Failed to fetch withdrawal history.');
                });
        });
    });
});



</script>
