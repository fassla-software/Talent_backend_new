@extends('layouts.app')

@php
$statusTexts = [
'SEND' => 'RECEIVED',
'CANCELLED' => 'INADMISSIBLE',
'ACCEPTED' => 'INSPECTED',
];
@endphp

@section('content')
<h1>Requests</h1>

<!-- Filter Form -->
<form method="GET" action="{{ url()->current() }}" id="filterForm">
    <div class="row mb-3">

        <!-- Filter by Status -->
        <div class="col-md-2">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control filter-input">
                <option value="">Select Status</option>
                <option value="SEND" {{ request('status') == 'SEND' ? 'selected' : '' }}>RECEIVED</option>
                <option value="ACCEPTED" {{ request('status') == 'ACCEPTED' ? 'selected' : '' }}>INSPECTED</option>
                <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>INADMISSIBLE</option>
                <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                @foreach ($statuses as $status)
                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter by City -->
        <div class="col-md-2">
            <label for="city">City</label>
            <select id="city" name="city" class="form-control filter-input">
                <option value="">Select City</option>
                @foreach ($cities as $city)
                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter by Area -->
        <div class="col-md-2">
            <label for="area">Area</label>
            <select id="area" name="area" class="form-control filter-input">
                <option value="">Select Area</option>
                @foreach ($areas as $area)
                <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>{{ $area }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter by Phone -->
        <div class="col-md-2">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" class="form-control filter-input" value="{{ request('phone') }}" placeholder="Enter phone number">
        </div>

        <!-- Filter by Assigned To -->
        <div class="col-md-2">
            <label for="assigned_to">Assigned To</label>
            <select id="assigned_to" name="assigned_to" class="form-control filter-input">
                <option value="">Select Envoy</option>
                @foreach ($envoyUsers as $envoy)
                <option value="{{ $envoy->id }}" {{ request('assigned_to') == $envoy->id ? 'selected' : '' }}>
                    {{ $envoy->name }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Filter by Date From -->
        <div class="col-md-1">
            <label for="date_from">Date From</label>
            <input type="date" id="date_from" name="date_from" class="form-control filter-input" value="{{ request('date_from') }}">
        </div>

        <!-- Filter by Date To -->
        <div class="col-md-1">
            <label for="date_to">Date To</label>
            <input type="date" id="date_to" name="date_to" class="form-control filter-input" value="{{ request('date_to') }}">
        </div>

        <!-- Filter & Clear Buttons -->
        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <a href="{{ url()->current() }}" class="btn btn-secondary w-100">Clear</a>
        </div>
    </div>
</form>

<!-- JavaScript for Dynamic Filtering -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let filterInputs = document.querySelectorAll('.filter-input');

        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });
    });
</script>

<!-- Bulk Delete Button -->
<div class="mb-3">
    <button id="bulkDeleteBtn" class="btn btn-danger" disabled>Delete</button>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th>ID</th>
            <th>User Name</th>
            <th>User Phone</th>
            <th>Area</th>
            <th>City</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Assigned To</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($requests as $request)
        <tr>
            <td><input type="checkbox" class="rowCheckbox" value="{{ $request['id'] }}"></td>
            <td>{{ $request['id'] }}</td>
            <td>{{ $request['requestor']['name'] ?? 'N/A' }}</td>
            <td>{{ $request['requestor']['phone'] ?? 'N/A' }}</td>
            <td>{{ $request['area'] }}</td>
            <td>{{ $request['city'] }}</td>
            <td>
                <span class="badge 
                        {{ $request['status'] === 'APPROVED' ? 'bg-success' : '' }}
                        {{ $request['status'] === 'CANCELLED' ? 'bg-danger' : '' }}
                        {{ $request['status'] === 'ASSIGNED' ? 'bg-warning' : '' }}
                        {{ $request['status'] === 'REJECTED' ? 'bg-danger' : '' }}
                        {{ $request['status'] === 'ACCEPTED' ? 'bg-success' : '' }}
                        {{ $request['status'] === 'SEND' ? 'bg-info' : '' }}
                        {{ $request['status'] === 'PENDING' ? 'bg-secondary' : '' }}
                        text-white">
                    {{ $statusTexts[$request['status']] ?? $request['status'] }}
                </span>
            </td>
			<td>
    			{{ \Carbon\Carbon::parse($request['createdAt'])->setTimezone('Africa/Cairo')->format('d M Y, h:i A') }}
			</td>
            <td>{{ $request['assigned_envoy'] }}</td>
            <td>
                <button
                    class="btn btn-primary btn-view-details"
                    data-request="{{ json_encode($request) }}"
                    data-bs-toggle="modal"
                    data-bs-target="#detailsModal">
                    View
                </button>
                <button
                    class="btn btn-secondary btn-assign {{ in_array($request['status'], ['ACCEPTED', 'APPROVED', 'CANCELLED']) ? 'disabled' : '' }}"
                    data-request="{{ json_encode($request) }}"
                    data-bs-toggle="modal"
                    data-bs-target="#assignModal"
                    {{ in_array($request['status'], ['ACCEPTED', 'APPROVED', 'CANCELLED']) ? 'disabled' : '' }}>
                    Assign
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="10">No requests found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- Add pagination links -->
<div class="d-flex justify-content-start">
    {{ $requests->links() }}
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailsModalLabel">Request Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="requestDetails" class="p-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="acceptRequest">Accept</button>
                <button type="button" class="btn btn-danger" id="rejectRequest">Reject</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="assignModalLabel">Assign Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="assignRequestDetails" class="p-3"></div>
                <div class="mb-3">
                    <strong>Assign to User:</strong>
                    <select id="assignUserSelect" class="form-control">
                        @foreach($envoyUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} {{ $user->last_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="saveAssignRequest">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox -->
<div id="lightbox" class="lightbox">
    <span class="lightbox-close">&times;</span>
    <img id="lightboxImage" class="lightbox-content" alt="Preview">
</div>

<style>
    #detailsModal .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    .modal-body div.mb-3 {
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 8px;
        margin-bottom: 16px;
    }

    .modal-body strong {
        display: inline-block;
        width: 150px;
        color: #495057;
    }

    .modal-body img {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .modal-content {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        border-bottom: 0;
    }

    .modal-footer {
        border-top: 0;
    }

    .modal-body .badge {
        font-size: 0.875rem;
        margin-right: 5px;
    }

    .modal-title {
        color: white !important;
    }

    .items-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .item img {
        width: 50px;
        height: auto;
        border-radius: 5px;
        border: 1px solid #ddd;
    }

    .item span {
        display: inline-block;
    }

    .item .name {
        font-weight: bold;
    }

    .item .count {
        margin-left: 10px;
        color: #555;
    }

    .item .count {
        margin-left: 10px;
        color: #555;
    }

    .lightbox {
        display: none;
        position: fixed;
        z-index: 1060;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        justify-content: center;
        align-items: center;
    }

    .lightbox-content {
        max-width: 90%;
        max-height: 90%;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        transition: transform 0.3s ease-in-out;
        cursor: zoom-in;
    }

    .lightbox-content:hover {
        transform: scale(1.05);
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 2rem;
        font-weight: bold;
        cursor: pointer;
        z-index: 1100;
    }

    .lightbox-close:hover {
        color: #ff0000;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== ASSIGN MODAL FUNCTIONALITY =====
        const assignButtons = document.querySelectorAll('.btn-assign');
        const assignDetailsContainer = document.getElementById('assignRequestDetails');
        let currentAssignRequestId;

        assignButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                if (this.classList.contains('disabled')) {
                    event.preventDefault();
                    return;
                }
                const requestData = JSON.parse(this.getAttribute('data-request'));
                currentAssignRequestId = parseInt(requestData.id, 10);

                const detailsHTML = `
                    <div class="mb-3"><strong>ID:</strong> ${requestData.id}</div>
                    <div class="mb-3"><strong>User Name:</strong> ${requestData.user_name}</div>
                    <div class="mb-3"><strong>Status:</strong> 
                        <span class="badge ${requestData.status === 'SEND' ? 'bg-success' : 'bg-warning'}">${requestData.status}</span>
                    </div>
                `;
                assignDetailsContainer.innerHTML = detailsHTML;
            });
        });

        document.getElementById('saveAssignRequest').addEventListener('click', function() {
            const assignedUserId = parseInt(document.getElementById('assignUserSelect').value, 10);

            if (isNaN(assignedUserId) || assignedUserId <= 0) {
                alert('Please select a valid user');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to assign this request.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, assign it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    assignRequest(assignedUserId)
                        .then(() => {
                            Swal.fire(
                                'Assigned!',
                                'The request has been assigned and notified.',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        })
                        .catch(error => {
                            console.error("Assignment failed:", error);
                            Swal.fire('Error', 'Something went wrong during assignment.', 'error');
                        });
                }
            });
        });

        function assignRequest(inspectorId) {
            const data = {
                request_id: currentAssignRequestId,
                inspector_id: inspectorId
            };

            console.log("Sending data to the API:", JSON.stringify(data));

            return fetch('https://app.talentindustrial.com/plumber/request/assign', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer YOUR_API_TOKEN',
                    },
                    body: JSON.stringify(data),
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Parsed Response from Assign API:", data);

                    if (data) {
                        return fetch(`https://app.talentindustrial.com/api/v1/get-device-token/${inspectorId}`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Authorization': 'Bearer YOUR_API_TOKEN',
                                },
                            })
                            .then(response => response.json())
                            .then(tokenData => {
                                if (tokenData.device_token) {
                                    const notificationData = {
                                        token: tokenData.device_token,
                                        title: "طلب جديد",
                                        body: "تم إسناد طلب جديد إليك. يرجى فتح التطبيق للاطلاع على التفاصيل.",
                                        user_id: inspectorId
                                    };

                                    console.log("Sending notification:", JSON.stringify(notificationData));

                                    return fetch('https://app.talentindustrial.com/api/v1/send-notification', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Authorization': 'Bearer YOUR_API_TOKEN',
                                            },
                                            body: JSON.stringify(notificationData),
                                        })
                                        .then(response => response.json())
                                        .then(notificationResponse => {
                                            console.log("Notification Response:", notificationResponse);
                                        });
                                } else {
                                    console.error("No device token found for inspector.");
                                }
                            });
                    } else {
                        console.log("Failed to assign request:", data);
                        throw new Error("Assignment failed");
                    }
                });
        }
    });

    // ===== DETAILS MODAL FUNCTIONALITY =====
    document.addEventListener('DOMContentLoaded', function() {
        const viewButtons = document.querySelectorAll('.btn-view-details');
        const detailsContainer = document.getElementById('requestDetails');
        let currentRequestId;

        viewButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                const requestData = JSON.parse(this.getAttribute('data-request'));
                currentRequestId = parseInt(requestData.id, 10);

                const totalPoints = requestData.items.reduce((sum, item) => sum + (item.count * item.subcategory.points), 0);

                const detailsHTML = `
                    <div class="mb-3"><strong>ID:</strong> ${requestData.id}</div>
                    <div class="mb-3"><strong>User Name:</strong> ${requestData.user_name}</div>
                    <div class="mb-3"><strong>User Phone:</strong> ${requestData.user_phone}</div>
                    <div class="mb-3"><strong>Nationality ID:</strong> ${requestData.nationality_id || 'N/A'}</div>
                    <div class="mb-3"><strong>Area:</strong> ${requestData.area}</div>
                    <div class="mb-3"><strong>City:</strong> ${requestData.city}</div>
                    <div class="mb-3"><strong>Address:</strong> ${requestData.address || 'N/A'}</div>
                    <div class="mb-3"><strong>Seller Name:</strong> ${requestData.seller_name || 'N/A'}</div>
                    <div class="mb-3"><strong>Seller Phone:</strong> ${requestData.seller_phone || 'N/A'}</div>
                    <div class="mb-3"><strong>Certificate ID:</strong> ${requestData.certificate_id || 'N/A'}</div>
                    <div class="mb-3"><strong>Inspection Date:</strong> ${requestData.inspection_date ? new Date(requestData.inspection_date).toLocaleString() : 'N/A'}</div>
                    <div class="mb-3"><strong>Inspector ID:</strong> ${requestData.inspector_id || 'N/A'}</div>
                    <div class="mb-3"><strong>Description:</strong> ${requestData.description || 'N/A'}</div>
                    <div class="mb-3"><strong>Comment:</strong> ${requestData.comment || 'N/A'}</div>
                    <div class="mb-3"><strong>Note:</strong> ${requestData.note || 'N/A'}</div>
                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span class="badge ${
                            requestData.status === 'CANCELLED' ? 'bg-danger' :
                            requestData.status === 'ASSIGNED' ? 'bg-warning' :
                            requestData.status === 'APPROVED' ? 'bg-success' :
                            requestData.status === 'PENDING' ? 'bg-secondary' :
                            requestData.status === 'ACCEPTED' ? 'bg-success' :
                            requestData.status === 'SEND' ? 'bg-info' :
                            requestData.status === 'REJECTED' ? 'bg-danger' : 'bg-secondary'
                        }">
                            ${requestData.status}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Items:</strong>
                        <div class="items-container">
                            ${requestData.items
                                .map(
                                    (item) => `
                                    <div class="item">
                                        <img src="${item.subcategory.image}" alt="${item.subcategory.name}">
                                        <span class="name">Name: ${item.subcategory.name}</span>
                                        <span class="count">Count: ${item.count}</span>
                                        <span class="count">X ${item.subcategory.points}</span>
                                        <span class="count">= ${item.count*item.subcategory.points}</span>
                                    </div>
                                `
                                )
                                .join('')}
                            <span style="margin-top: 10px; margin-left: auto;" class="count">Total Points ${totalPoints}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Distance:</strong>
                        ${requestData.distance
                            ? (requestData.distance > 1000 ? 'Far' : 'Close')
                            : 'N/A'}
                    </div>
                    <div class="mb-3">
                        <strong>Images:</strong>
                        <div class="d-flex flex-wrap">
                            ${requestData.images.map(img => `
                                <a href="${img}" class="lightbox-trigger">
                                    <img src="${img}" class="img-thumbnail me-2" style="width: 150px; cursor: pointer;">
                                </a>
                            `).join('')}
                        </div>
                    </div>
                    <div class="mb-3"><strong>Inspection Images:</strong>
                        <div class="d-flex flex-wrap">
                            ${requestData.inspection_images.length > 0
                                ? requestData.inspection_images.map(img => `
                                    <a href="${img}" class="lightbox-trigger">
                                        <img src="${img}" class="img-thumbnail me-2" style="width: 150px; cursor: pointer;">
                                    </a>
                                `).join('')
                                : 'No inspection images available'}
                        </div>
                    </div>
                    <div class="mb-3"><strong>Created At:</strong> ${new Date(requestData.createdAt).toLocaleString()}</div>
                    <div class="mb-3"><strong>Updated At:</strong> ${new Date(requestData.updatedAt).toLocaleString()}</div>
                `;

                detailsContainer.innerHTML = detailsHTML;

                const acceptButton = document.getElementById('acceptRequest');
                const rejectButton = document.getElementById('rejectRequest');

                if (requestData.status === 'ACCEPTED') {
                    acceptButton.disabled = false;
                    rejectButton.disabled = false;
                } else {
                    acceptButton.disabled = true;
                    rejectButton.disabled = true;
                }
            });
        });

        document.getElementById('acceptRequest').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to accept this request.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, accept it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateStatus('APPROVED');
                    Swal.fire(
                        'Accepted!',
                        'The request has been accepted.',
                        'success'
                    );
                }
            });
        });

        document.getElementById('rejectRequest').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to reject this request.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reject it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateStatus('CANCELLED');
                    Swal.fire(
                        'Rejected!',
                        'The request has been rejected.',
                        'error'
                    );
                }
            });
        });

        function updateStatus(status) {
            fetch('https://app.talentindustrial.com/plumber/request/approve', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer YOUR_API_TOKEN',
                    },
                    body: JSON.stringify({
                        request_id: currentRequestId,
                        request_status: status
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Status updated:', data);
                })
                .catch(error => {
                    alert('Error updating status: ' + error);
                });
        }
    });

    // ===== LIGHTBOX FUNCTIONALITY (FIXED) =====
    document.addEventListener('DOMContentLoaded', function() {
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = document.getElementById('lightboxImage');
        const closeLightbox = document.querySelector('.lightbox-close');
        const sidebar = document.querySelector('.fixed-sidebar .app-sidebar');
        const footer = document.querySelector('.app-theme-white .app-footer .app-footer-inner');
        const header = document.querySelector('.app-theme-white .app-header');

        let isDragging = false;
        let lastX, lastY;
        let currentModal = null;

        // Intercept clicks on image links
        document.body.addEventListener('click', function(e) {
            const linkElement = e.target.closest('a.lightbox-trigger');
            if (linkElement) {
                e.preventDefault();
                const imageUrl = linkElement.href;
                lightboxImage.src = imageUrl;
                lightbox.style.display = 'flex';

                // Store reference to the currently open modal
                currentModal = document.querySelector('.modal.show');

                // Hide the modal if it's open
                if (currentModal) {
                    currentModal.style.display = 'none';
                }

                // Hide the sidebar, footer, and header
                if (sidebar) sidebar.style.display = 'none';
                if (footer) footer.style.display = 'none';
                if (header) header.style.display = 'none';
            }
        });

        // Function to close lightbox and restore modal
        function closeLightboxAndRestoreModal() {
            lightbox.style.display = 'none';

            // Restore the sidebar, footer, and header
            if (sidebar) sidebar.style.display = '';
            if (footer) footer.style.display = '';
            if (header) header.style.display = '';

            // Restore the modal if it was open
            if (currentModal) {
                currentModal.style.display = 'block';
                currentModal = null;
            }
        }

        // Close the lightbox when the close icon is clicked
        closeLightbox.addEventListener('click', function() {
            closeLightboxAndRestoreModal();
        });

        // Close the lightbox when clicking outside the image
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                closeLightboxAndRestoreModal();
            }
        });

        // Close lightbox with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && lightbox.style.display === 'flex') {
                closeLightboxAndRestoreModal();
            }
        });

        // Allow zooming in and out of the image by dragging
        lightboxImage.addEventListener('mousedown', function(e) {
            isDragging = true;
            lastX = e.clientX;
            lastY = e.clientY;
            e.preventDefault();
        });

        document.addEventListener('mousemove', function(e) {
            if (isDragging) {
                const deltaX = e.clientX - lastX;
                const deltaY = e.clientY - lastY;

                lightboxImage.style.transform = `translate(${deltaX}px, ${deltaY}px)`;

                lastX = e.clientX;
                lastY = e.clientY;
            }
        });

        document.addEventListener('mouseup', function() {
            isDragging = false;
            lightboxImage.style.transform = '';
        });
    });

    // ===== BULK DELETE FUNCTIONALITY =====
    document.addEventListener('DOMContentLoaded', function() {
        let selectedIds = [];
        const selectAllCheckbox = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateSelectedIds();
        });

        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectedIds();
            });
        });

        function updateSelectedIds() {
            selectedIds = Array.from(rowCheckboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => parseInt(checkbox.value));

            bulkDeleteBtn.disabled = selectedIds.length === 0;

            const checkedCount = selectedIds.length;
            const totalCount = rowCheckboxes.length;
            selectAllCheckbox.checked = checkedCount === totalCount;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
        }

        bulkDeleteBtn.addEventListener('click', function() {
            if (selectedIds.length === 0) return;

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete ${selectedIds.length} request(s). This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteRequests(selectedIds);
                }
            });
        });

        function deleteRequests(ids) {
            fetch('https://app.talentindustrial.com/plumber/request/bulk-delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer YOUR_API_TOKEN',
                },
                body: JSON.stringify({ ids: ids }),
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire('Deleted!', 'The requests have been deleted.', 'success')
                .then(() => window.location.reload());
            })
            .catch(error => {
                console.error('Error deleting requests:', error);
                Swal.fire('Error', 'Something went wrong while deleting.', 'error');
            });
        }
    });
</script>
@endsection
