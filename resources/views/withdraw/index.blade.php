@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 d-flex justify-content-between">
        <span>Withdraw Requests</span>
        <!-- Download Excel Button -->
        <a href="{{ route('withdraw.download') }}" class="btn btn-success">Download Excel</a>
    </h1>

    @hasPermission('plumber.withdrawal.edit')
    <!-- Upload Excel Form -->
    <form action="{{ route('withdraw.upload') }}" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="input-group">
            <input type="file" name="file" class="form-control" accept=".csv, .xlsx" required>
            <button type="submit" class="btn btn-primary">Upload Excel</button>
        </div>
    </form>
@endhasPermission

    <form method="GET" action="{{ route('withdraw.index') }}" class="mb-4">
        <label for="status">Filter by Status: </label>
        <select name="status" id="status" class="form-control" style="width: auto; display: inline-block;">
            <option value="">All</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        
        <label for="transaction_type">Filter by Transaction Type: </label>
        <select name="transaction_type" id="status" class="form-control" style="width: auto; display: inline-block;">
            <option value="">All</option>
            <option value="wallet" {{ request('transaction_type') === 'wallet' ? 'selected' : '' }}>Wallet</option>
            <option value="bank" {{ request('transaction_type') === 'bank' ? 'selected' : '' }}>Bank</option>
            <option value="meeza" {{ request('transaction_type') === 'meeza' ? 'selected' : '' }}>Meeza</option>
        </select>
    
    <label for="name" style="margin-left: 10px;">Filter by Name : </label>
    <input type="text" name="name" id="name" value="{{ request('name') }}" 
           class="form-control" style="width: 80px; display: inline-block;" 
           >
    <!-- Filter by Phone -->
        <div class="col-md-3">
            <input type="text" name="phone" class="form-control" value="{{ request('phone') }}" placeholder="Filter by Phone">
        </div>
                
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        
    </form>
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
                <td>{{ ucfirst($withdraw['status']) }}</td>
                <td>
    
    @hasPermission('plumber.withdrawal.edit')

<div class="dropdown" style="display: inline; position: relative;">
    @if($withdraw['status'] === 'pending')
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
            @foreach(['pending', 'approved', 'rejected'] as $status)
            <li>
                <form 
                    action="{{ route('withdraw.updateStatus', $withdraw['id']) }}" 
                    method="POST" 
                    id="status-form-{{ $withdraw['id'] }}-{{ $status }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $status }}">
                    <a 
                        class="dropdown-item" 
                        href="javascript:void(0)" 
                        onclick="document.getElementById('status-form-{{ $withdraw['id'] }}-{{ $status }}').submit();">
                        {{ ucfirst($status) }}
                    </a>
                </form>
            </li>
            @endforeach
        </ul>
    @else
        <button 
            class="btn btn-secondary btn-sm" 
            type="button"
            disabled
            style="z-index: 1050;">
            {{ ucfirst($withdraw['status']) }}
        </button>
    @endif
</div>
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
                <td colspan="8" class="text-center">No withdraw requests available.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
            <!-- Pagination (if needed) -->
        {{ $data->links() }}
</div>
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
                                        max-width: 900px; /* Optional: Limit the maximum width of the container */
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
