@extends('layouts.app')

@section('content')


<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Plumber Users</h1>
        @if(isset($activeYear))
            <div class="badge bg-success" style="font-size: 1rem; padding: 10px 20px;">
                <i class="fa fa-calendar"></i> Active Year: {{ $activeYear->year }}
            </div>
        @else
            <div class="badge bg-warning text-dark" style="font-size: 1rem; padding: 10px 20px;">
                <i class="fa fa-exclamation-triangle"></i> No Active Fiscal Year
            </div>
        @endif
    </div>

    @if(request('fiscal_year_id') && (!$activeYear || request('fiscal_year_id') != $activeYear->id))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fa fa-info-circle"></i> <strong>Viewing Historical Year:</strong> You are viewing archived data. All editing actions are disabled for historical years.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

<div class="mb-3">
        <input type="checkbox" id="selectAll" style="margin-right: 10px;">
        <label for="selectAll">Select All</label>
        <button id="bulkDownloadButton" class="btn btn-primary btn-sm ml-3">Download Selected</button>
        @hasPermission('admin.fiscal.manage')
        <button type="button" class="btn btn-danger btn-sm ml-3" data-toggle="modal" data-target="#resetFiscalYearModal">
            <i class="fa fa-refresh"></i> Start New Fiscal Year
        </button>
        @endhasPermission
    </div>
      <!-- Filter by Status -->
<form method="GET" action="{{ route('admin.plumberUsers') }}" class="mb-4">
    <label for="status">Filter by Status: </label>
    <select name="status" id="status" class="form-control" style="width: auto; display: inline-block;">
        <option value="">All</option>
        <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
        <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Approved</option>
        <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Rejected</option>
    </select>

    <label for="nationality_id" style="margin-left: 10px;">Filter by Nationality ID: </label>
    <input type="text" name="nationality_id" id="nationality_id" value="{{ request('nationality_id') }}" 
           class="form-control" style="width: 80px; display: inline-block;">

    <label for="name" style="margin-left: 10px;">Filter by Name: </label>
    <input type="text" name="name" id="name" value="{{ request('name') }}" 
           class="form-control" style="width: 80px; display: inline-block;">

    <label for="city" style="margin-left: 10px;">Filter by City: </label>
    <select name="city" id="city" class="form-control" style="width: 200px; display: inline-block;">
        <option value="">-- Select City --</option>
        @foreach ($allCities as $city)
            <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>
                {{ $city }}
            </option>
        @endforeach
    </select>

    <label for="created_month" style="margin-left: 10px;">Filter by Month of Creation: </label>
    <select name="created_month" id="created_month" class="form-control" style="width: auto; display: inline-block;">
        <option value="">-- Select Month --</option>
        @foreach ($availableMonths as $month)
            <option value="{{ $month }}" {{ request('created_month') === $month ? 'selected' : '' }}>
                {{ date('F Y', strtotime($month . '-01')) }}
            </option>
        @endforeach
    </select>

    <label for="phone" style="margin-left: 10px;">Filter by Phone: </label>
    <input type="text" name="phone" class="form-control" value="{{ request('phone') }}" placeholder="Filter by Phone" style="width: 150px; display: inline-block;">

    <label for="fiscal_year_id" style="margin-left: 10px;"><strong>Fiscal Year:</strong> </label>
    <select name="fiscal_year_id" id="fiscal_year_id" class="form-control" style="width: auto; display: inline-block;">
        <option value="">📅 Current Year {{ isset($activeYear) ? '(' . $activeYear->year . ')' : '' }}</option>
        @foreach ($allFiscalYears as $fy)
            <option value="{{ $fy->id }}" {{ request('fiscal_year_id') == $fy->id ? 'selected' : '' }}>
                {{ $fy->is_active ? '✓ ' : '📁 ' }}{{ $fy->year }} {{ $fy->is_active ? '(Active)' : '(Historical)' }}
            </option>
        @endforeach
    </select>

    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
</form>


    @if ($plumbers->isEmpty())
    @else
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                            <th><input type="checkbox" id="selectAllTop"></th>

                    <th>ID</th>
                    <th>User Name</th>
                    <th>City</th>
                    <th>User Status</th>
                    <th>Actions</th> <!-- Actions column -->
                </tr>
            </thead>
            <tbody>
        @if ($plumbers->isEmpty())
    <p>No plumbers found with the selected filters.</p>
		@else
                @foreach ($plumbers as $plumber)
                    <tr>
        <td>
                            <input type="checkbox" class="user-checkbox" value="https://app.talentindustrial.com/plumber/plumber/report/{{ $plumber->user_id }}/download">
                        </td>
                        <td>{{ $plumber->user->id }}</td>
                        <td>{{ $plumber->user->name ?? 'No User' }}</td>
                        <td>{{ $plumber->city ?? 'No City' }}</td>
<td>
    <span class="badge rounded-pill 
        @if ($plumber->user->status === 'APPROVED') bg-success
        @elseif ($plumber->user->status === 'PENDING') bg-warning text-dark
        @elseif ($plumber->user->status === 'REJECTED') bg-danger
        @else bg-secondary
        @endif
    ">
        {{ $plumber->user->status ?? 'No Status' }}
    </span>
</td>
                        
<td>
                            <!-- View Button (to open new page) -->
                            <a href="{{ route('admin.plumberUsers.show', $plumber->id) }}" class="btn btn-warning">
                                <i class="fa fa-eye"></i>
                            </a>

                            <!-- Report Button (always visible) -->
<a style="font-size: 1rem;" href="https://app.talentindustrial.com/plumber/plumber/report/{{ $plumber->user_id }}/download" 
   class="btn btn-info btn-sm" target="_blank">
   <i class="fa fa-file-download"></i>
</a>
    <!-- Edit Button (visible only for active year) -->
@if(!request('fiscal_year_id') || (isset($activeYear) && request('fiscal_year_id') == $activeYear->id))
<button data-toggle="modal" data-target="#plumberModalEdit{{ $plumber->user_id }}" class="btn btn-success">
    <i class="fa fa-edit"></i>
</button>
@else
<button class="btn btn-secondary" disabled title="Editing disabled for historical years">
    <i class="fa fa-edit"></i>
</button>
@endif

    <!-- Approve/Reject Buttons (visible only for PENDING status AND active year) -->
    @if ($plumber->user->status === 'PENDING')
        @if(!request('fiscal_year_id') || (isset($activeYear) && request('fiscal_year_id') == $activeYear->id))
            <!-- Approve Button -->
            <form id="approve-form-{{ $plumber->id }}" 
                  action="{{ route('admin.plumberUsers.approve', $plumber->id) }}" 
                  method="POST" style="display:inline-block;">
                @csrf
                @method('PUT')
                <button type="button" class="btn btn-success btn-sm approve-button">Approve</button>
            </form>

            <!-- Reject Button -->
            <form id="reject-form-{{ $plumber->id }}" 
                  action="{{ route('admin.plumberUsers.reject', $plumber->id) }}" 
                  method="POST" style="display:inline-block;">
                @csrf
                @method('PUT')
                <button type="button" class="btn btn-danger btn-sm reject-button">Reject</button>
            </form>
        @else
            <button class="btn btn-secondary btn-sm" disabled title="Actions disabled for historical years">
                <i class="fa fa-check"></i> Approve
            </button>
            <button class="btn btn-secondary btn-sm" disabled title="Actions disabled for historical years">
                <i class="fa fa-times"></i> Reject
            </button>
        @endif
    @endif
</td>
<!-- Modal -->
@if ($plumbers->isNotEmpty())  
    <div class="modal fade" id="plumberModalEdit{{ $plumber->user_id }}" tabindex="-1" role="dialog" aria-labelledby="plumberModalEditLabel{{ $plumber->user_id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="plumberModalEditLabel{{ $plumber->user_id }}">Plumber Details (ID: {{ $plumber->user_id }})</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="save-changes-form" method="post" action="{{ route('admin.plumberUsers.update', $plumber->user_id) }}">
                        @csrf
                        @method('patch')

                        <!-- Name Field -->
                        <label>Name</label>
                        <div class="input-group mb-3">
                            <input type="text" name="name" value="{{ $plumber->user->name }}" class="form-control" placeholder="Name" aria-label="Name">
                        </div>

                        <!-- Phone Field -->
                        <label>Phone</label>
                        <div class="input-group mb-3">
                            <input type="text" name="phone" value="{{ $plumber->user->phone }}" class="form-control" placeholder="Phone" aria-label="Phone">
                        </div>
                        
                        
                       <!-- Nationality ID Field -->
                        <label>Nationality ID</label>
                        <div class="input-group mb-3">
                            <input type="text" name="nationality_id" value="{{ $plumber->nationality_id }}" class="form-control" placeholder="Nationality ID" aria-label="Nationality ID">
                        </div>

                        <!-- Gift Points Field -->
                        <label>Gift Points</label>
                        <div class="input-group mb-3">
                            <input type="number" name="gift_points" value="{{ $plumber->gift_points }}" class="form-control" placeholder="Gift Points" aria-label="Gift Points">
                        </div>

                        <!-- Fixed Points Field -->
                        <label>Fixed Points</label>
                        <div class="input-group mb-3">
                            <input type="number" name="fixed_points" value="{{ $plumber->fixed_points }}" class="form-control" placeholder="Fixed Points" aria-label="Fixed Points">
                        </div>

                        
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
@else
    <div class="text-center">
        <p>No plumbers found with the selected filters. Please adjust the filter criteria.</p>
    </div>
@endif



<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Save Changes button handler
        document.querySelector('.save-changes-button').addEventListener('click', function () {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to save changes to this plumber\'s details.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save changes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('save-changes-form').submit();
                }
            });
        });
    });
</script>

                          <!-- 
<form method="POST" class="delete-form"  style="display: inline"  data-route="{{route('admin.plumberUsers.destroy', $plumber->id)}}">
    @method('DELETE')
    @csrf

    <button type="submit" class="btn btn-danger ">
        <i class="fa fa-trash"></i>
    </button>
</form>
-->




                        </td>
                    </tr>

                    <!-- Modal for each plumber -->
                    <div class="modal fade" id="plumberModal{{ $plumber->id }}" tabindex="-1" role="dialog" aria-labelledby="plumberModalLabel{{ $plumber->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="plumberModalLabel{{ $plumber->id }}">Plumber Details (ID: {{ $plumber->id }})</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Displaying Plumber Details in the Modal -->
                                    <p><strong>Name:</strong> {{ $plumber->user->name }}</p>
                                    <p><strong>City:</strong> {{ $plumber->city }}</p>
                                    <p><strong>Area:</strong> {{ $plumber->area }}</p>
                                    <p><strong>Nationality:</strong> {{ $plumber->nationality_id ?? 'No Nationality' }}</p>
                                    <p><strong>Phone:</strong> {{ $plumber->user->phone ?? 'No Phone' }}</p>
<p><strong>Verified?</strong> 
    {{ isset($plumber->is_verified) && $plumber->is_verified ? 'Yes' : 'No' }}
</p>
                                    <p><strong>Instant Withdrawal:</strong> {{ $plumber->instant_withdrawal }}</p>
                                    <p><strong>Withdraw Money:</strong> {{ $plumber->withdraw_money }}</p>
                                    <p><strong>Gift Points:</strong> {{ $plumber->gift_points }}</p>
                                    <p><strong>Fixed Points:</strong> {{ $plumber->fixed_points }}</p>
                                    <p><strong>Created At:</strong> {{ \Carbon\Carbon::parse($plumber->created_at)->format('Y-m-d H:i:s') }}</p>
                                    <p><strong>Updated At:</strong> {{ \Carbon\Carbon::parse($plumber->updated_at)->format('Y-m-d H:i:s') }}</p>
                                    
                                    <!-- Displaying images in the modal -->
                                   <p><strong>Nationality Image 1:</strong></p>
@if($plumber->nationality_image1)
    <img src="https://app.talentindustrial.com/plumber/uploads/{{ $plumber->nationality_image1 }}" alt="Nationality Image 1" class="img-fluid mb-2" style="max-height: 200px;">
@else
    <p>No Image Available</p>
@endif

<p><strong>Nationality Image 2:</strong></p>
@if($plumber->nationality_image2)
    <img src="https://app.talentindustrial.com/plumber/uploads/{{ $plumber->nationality_image2 }}" alt="Nationality Image 2" class="img-fluid mb-2" style="max-height: 200px;">
@else
    <p>No Image Available</p>
@endif

<p><strong>Plumber Image:</strong></p>
@if($plumber->image)
    <img src="https://app.talentindustrial.com/plumber/uploads/{{ $plumber->image }}" alt="Plumber Image" class="img-fluid mb-2" style="max-height: 200px;">
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
                @endforeach
@endif
            </tbody>
        </table>
        <!-- Pagination (preserves all filters) -->
        {{ $plumbers->appends(request()->query())->links() }}
    @endif
</div>

<!-- Start New Fiscal Year Modal -->
<div class="modal fade" id="resetFiscalYearModal" tabindex="-1" role="dialog" aria-labelledby="resetFiscalYearModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetFiscalYearModalLabel">Start New Fiscal Year</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.plumberUsers.resetFiscalYear') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> Starting a new fiscal year will archive all current points and reset everyone to zero for the new year. This action cannot be easily undone.
                    </div>
                    <div class="form-group">
                        <label for="new_year">New Fiscal Year (e.g., 2026)</label>
                        <input type="text" name="year" id="new_year" class="form-control" placeholder="Enter Year" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you absolutely sure?')">Initialize New Year</button>
                </div>
            </form>
        </div>
    </div>
</div>


@include('admin.plumberUsersEdit')



@endsection

@section('scripts')
<!-- Include Bootstrap JS (for Modal functionality) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

@endsection
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAll');
        const selectAllTop = document.getElementById('selectAllTop');
        const checkboxes = document.querySelectorAll('.user-checkbox');
        const bulkDownloadButton = document.getElementById('bulkDownloadButton');

        // Toggle all checkboxes
        function toggleAllCheckboxes(checked) {
            checkboxes.forEach(checkbox => checkbox.checked = checked);
        }

        selectAll.addEventListener('change', () => toggleAllCheckboxes(selectAll.checked));
        selectAllTop.addEventListener('change', () => toggleAllCheckboxes(selectAllTop.checked));

        // Bulk download handler
        bulkDownloadButton.addEventListener('click', function () {
            const selectedLinks = Array.from(checkboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);

            if (selectedLinks.length === 0) {
                alert('Please select at least one user to download reports.');
                return;
            }

            selectedLinks.forEach(link => {
                // Create an <a> element dynamically for each download link
                const a = document.createElement('a');
                a.href = link;
                a.download = link.split('/').pop(); // Set the filename for the download
                a.style.display = 'none'; // Hide the <a> element
                document.body.appendChild(a);
                a.click(); // Programmatically trigger a click event
                document.body.removeChild(a); // Remove the <a> element after the download
            });
        });

        // Approve/Reject handlers
        document.querySelectorAll('.approve-button').forEach(button => {
            button.addEventListener('click', function () {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are about to approve this plumber.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
            });
        });

        document.querySelectorAll('.reject-button').forEach(button => {
            button.addEventListener('click', function () {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are about to reject this plumber.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, reject!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
            });
        });
    });
</script>
<style>
/* Modal Styles */
.modal-content {
    background-color: #ffffff;
    border: 1px solid #28a745; /* Green border */
    border-radius: 8px;
    padding: 10px;
}

.modal-header {
    background-color: #28a745; /* Green header */
    color: white;
    border-bottom: 1px solid #28a745; /* Thicker green border at the bottom */
    font-size: 1.2rem; /* Smaller font for the header */
    padding: 10px;
}

.modal-footer {
    border-top: 1px solid #28a745; /* Thicker green border at the top */
    padding: 10px;
}

.modal-title {
    font-weight: bold;
    font-size: 1.1rem; /* Slightly smaller title size */
}

/* Styling for the label and content pairs */
.modal-body p {
    font-size: 1rem; /* Smaller font size for readability */
    margin: 10px 0; /* Reduce spacing between each label and value */
    padding-bottom: 10px; /* Add padding at the bottom */
    border-bottom: 1px solid #f1f1f1; /* Add a light gray line between each entry */
}

.modal-body p:last-child {
    border-bottom: none; /* Remove the bottom border on the last entry */
}

/* Green button styling */
.btn-info {
    background-color: #28a745; /* Green button */
    border-color: #28a745; /* Green button border */
    padding: 8px 15px;
    font-size: 0.9rem; /* Smaller font size */
    border-radius: 5px;
}

.btn-info:hover {
    background-color: #218838; /* Darker green on hover */
    border-color: #1e7e34; /* Darker green border on hover */
}

.btn-secondary {
    background-color: #6c757d; /* Grey color for Close button */
    border-color: #6c757d;
    padding: 8px 15px;
    font-size: 0.9rem; /* Smaller font size */
    border-radius: 5px;
}

.btn-secondary:hover {
    background-color: #5a6268; /* Darker grey on hover */
    border-color: #545b62;
}

/* Adding space between the image section and content */
.modal-body img {
    max-width: 100%;
    height: auto;
    margin-top: 5px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Lighter shadow for a subtle effect */
}
/* Modal Width */
.modal-dialog {
    max-width: 60%; /* Reduce width to 60% of the viewport */
    width: auto; /* Allow automatic width based on content */
}

/* Modal Content */
.modal-content {
    background-color: #ffffff;
    border: 1px solid #28a745; /* Green border */
    border-radius: 8px;
    padding: 8px; /* Smaller padding */
}

.modal-header {
    background-color: #28a745; /* Green header */
    color: white;
    border-bottom: 1px solid #28a745; /* Thicker green border at the bottom */
    font-size: 1rem; /* Smaller font for the header */
    padding: 8px;
}

.modal-footer {
    border-top: 1px solid #28a745; /* Thicker green border at the top */
    padding: 8px;
}

.modal-title {
    font-weight: bold;
    font-size: 1rem; /* Smaller title size */
}

/* Styling for the label and content pairs */
.modal-body p {
    font-size: 0.9rem; /* Smaller font size for readability */
    margin: 8px 0; /* Reduce spacing between each label and value */
    padding-bottom: 8px; /* Add padding at the bottom */
    border-bottom: 1px solid #f1f1f1; /* Add a light gray line between each entry */
}

.modal-body p:last-child {
    border-bottom: none; /* Remove the bottom border on the last entry */
}

/* Green button styling */
.btn-info {
    background-color: #28a745; /* Green button */
    border-color: #28a745; /* Green button border */
    padding: 6px 12px; /* Smaller padding */
    font-size: 0.85rem; /* Slightly smaller font size */
    border-radius: 4px; /* Less rounded */
}

.btn-info:hover {
    background-color: #218838; /* Darker green on hover */
    border-color: #1e7e34; /* Darker green border on hover */
}

.btn-secondary {
    background-color: #6c757d; /* Grey color for Close button */
    border-color: #6c757d;
    padding: 6px 12px; /* Smaller padding */
    font-size: 0.85rem; /* Slightly smaller font size */
    border-radius: 4px; /* Less rounded */
}

.btn-secondary:hover {
    background-color: #5a6268; /* Darker grey on hover */
    border-color: #545b62;
}

/* Adding space between the image section and content */
.modal-body img {
    max-width: 100%;
    height: auto;
    margin-top: 5px;
    border-radius: 5px; /* Smaller radius for images */
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1); /* Lighter shadow for a subtle effect */
}
/* Change the color of the modal title to white */
.modal-title {
    color: white !important; /* Make the title text white */
}

</style>
