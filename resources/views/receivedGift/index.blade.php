@extends('layouts.app')

@section('content')
    <h1>Received Gifts</h1>

    @if($gifts->isEmpty())
        <p>No gifts available.</p>
    @else
        <form method="GET" action="{{ route('received-gift.index') }}" class="mb-4">
            <label for="plumber_name" class="mr-2">Filter by User Name:</label>
            <input type="text" name="plumber_name" id="plumber_name" value="{{ request('plumber_name') }}" 
                   class="form-control d-inline-block" style="width: 120px;">

            <label for="gift_name" class="ml-2">Filter by Gift Name:</label>
            <input type="text" name="gift_name" id="gift_name" value="{{ request('gift_name') }}" 
                   class="form-control d-inline-block" style="width: 120px;">

            <label for="status" class="ml-2">Filter by Status:</label>
            <select name="status" id="status" class="form-control d-inline-block" style="width: 140px;">
                <option value="">Select Status</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm ml-2">Filter</button>
        </form>

           <form id="bulk-action-form" method="POST" action="{{ route('received-gift.bulkAction') }}">
    @csrf
    <input type="hidden" name="action" id="bulk-action-input">
    <input type="hidden" name="selected_gifts" id="selected-gifts-input"> <!-- Hidden input for gift IDs -->

    <button type="button" class="btn btn-success btn-sm" onclick="submitBulkAction('Delivered')">Deliver</button>
    <button type="button" class="btn btn-danger btn-sm" onclick="submitBulkAction('Rejected')">Reject</button>
    <button type="button" class="btn btn-warning btn-sm" onclick="submitBulkAction('Cancelled')">Cancel</button>
        <button type="button" class="btn btn-info btn-sm" onclick="submitBulkDownload()">Download</button>

</form>

        <table class="table table-striped table-bordered" style="width: 100%;">
        <thead style="background-color: white !important;">
    <tr>
        <th><input type="checkbox" id="select-all"></th> <!-- Bulk Select Checkbox -->
        <th>User Name</th>
        <th>Gift Name</th>
        <th>Points Required</th>
        <th>Gift Image</th>
        <th>Status</th>
        <th>Date & Time</th>
        <th>Actions</th>
    </tr>
</thead>


            <tbody>
                @foreach($gifts as $gift)
                    <tr>
            <td>
                <input type="checkbox" class="gift-checkbox" name="selected_gifts[]" value="{{ $gift->id }}">
            </td>
                        <td>{{ $gift['user']['name'] }}</td>
                        <td>{{ $gift['plumber_gift']['name'] }}</td>
                        <td>{{ $gift['plumber_gift']['points_required'] }}</td>
                      <td>
    @if(isset($gift['plumber_gift']['image']) && $gift['plumber_gift']['image'])
        <img src="https://app.talentindustrial.com/plumber/uploads/{{ $gift['plumber_gift']['image'] }}" 
             alt="Gift Image" class="gift-image" onclick="openImageModal(this)">
    @else
        <span>No Image Available</span>
    @endif
</td>


<td>
    <span class="status-badge status-{{ strtolower($gift['status']) }}">
        {{ $gift['status'] }}
    </span>
</td>
                        <td>{{ \Carbon\Carbon::parse($gift['createdAt'])->format('Y-m-d H:i:s') }}</td> <!-- New Date & Time Column -->

                      <td>
   <a class="btn btn-info btn-sm" 
       href="{{ route('received-gift.downloadGift', ['userId' => $gift['user_id']]) }}"
       data-gift-id="{{ $gift->id }}">
        <i class="fa fa-file-download"></i>
    </a>

    @if($gift['status'] == 'Pending')
        <form action="{{ route('received-gift.updateStatus', $gift->id) }}" method="POST" class="d-inline">
            @csrf
            @method('PUT')

            <input type="hidden" name="status" value="Delivered">
            <button type="submit" class="btn btn-success btn-sm">Deliver</button>
        </form>

        <form action="{{ route('received-gift.updateStatus', $gift->id) }}" method="POST" class="d-inline">
            @csrf
            @method('PUT')

            <input type="hidden" name="status" value="Rejected">
            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
        </form>

        <form action="{{ route('received-gift.updateStatus', $gift->id) }}" method="POST" class="d-inline">
            @csrf
            @method('PUT')

            <input type="hidden" name="status" value="Cancelled">
            <button type="submit" class="btn btn-warning btn-sm">Cancel</button>
        </form>
    @endif
</td>


                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $gifts->links() }}
    @endif

    <script>
        function confirmStatusChange(event, giftId) {
            var selectedStatus = event.target.value;
            var confirmationMessage = `Are you sure you want to change the status to ${selectedStatus}?`;

            if (confirm(confirmationMessage)) {
                document.getElementById('status-form-' + giftId).submit();
            } else {
                event.target.value = event.target.defaultValue;
            }
        }
    </script>

    <style>
        /* Table Styling */
        table {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }
        .table-striped tbody tr:nth-of-type(even) {
            background-color: white;
        }
       th {
    background-color: white !important;
    color: black !important;
    border-bottom: 2px solid #ddd; /* Optional: Light gray bottom border */
}
/* Small Image Display */
.gift-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
    cursor: pointer; /* Show clickable hand */
}

/* Modal Styling */
.image-modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    text-align: center;
}

/* Center the Image */
.image-modal img {
    max-width: 90%;
    max-height: 90%;
    margin-top: 4%;
    border-radius: 10px;
}

/* Close Button */
.image-modal .close {
    position: absolute;
    top: 20px;
    right: 30px;
    color: white;
    font-size: 30px;
    font-weight: bold;
    cursor: pointer;
}
.btn-sm {
    margin: 2px; /* Small spacing between buttons */
}

/* Status Badges */
.status-badge {
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    color: white;
    display: inline-block;
}

/* Status Colors */
.status-approved { background-color: #28a745; } /* Green */
.status-rejected { background-color: #dc3545; } /* Red */
.status-pending { background-color: #ffc107; color: black; } /* Yellow */
.status-cancelled { background-color: #6c757d; } /* Gray */
.status-delivered { background-color: #007bff; } /* Blue */

    </style>
    
    <!-- Image Modal -->
<div id="imageModal" class="image-modal">
    <span class="close" onclick="closeImageModal()">&times;</span>
    <img id="modalImage" src="" alt="Gift Image">
</div>

<script>
    function openImageModal(imgElement) {
        var modal = document.getElementById("imageModal");
        var modalImg = document.getElementById("modalImage");

        modal.style.display = "block"; // Show the modal
        modalImg.src = imgElement.src; // Set the modal image source
    }

    function closeImageModal() {
        document.getElementById("imageModal").style.display = "none";
    }

    // Close modal when clicking anywhere outside the image
    document.getElementById("imageModal").addEventListener("click", function(event) {
        if (event.target === this) {
            closeImageModal();
        }
    });
</script>

<script>
    document.getElementById("select-all").addEventListener("change", function() {
        let checkboxes = document.querySelectorAll(".gift-checkbox");
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    function submitBulkAction(status) {
        let selectedGifts = getSelectedGifts();
        if (selectedGifts.length === 0) {
            alert("Please select at least one gift.");
            return;
        }

        if (!confirm(`Are you sure you want to mark these gifts as ${status}?`)) return;

        // Set the selected gift IDs in the hidden input field
        document.getElementById("selected-gifts-input").value = JSON.stringify(selectedGifts);
        document.getElementById("bulk-action-input").value = status;

        // Submit the form
        document.getElementById("bulk-action-form").submit();
    }

   

   function submitBulkDownload() {
        let selectedGifts = getSelectedGifts();
        if (selectedGifts.length === 0) {
            alert("Please select at least one gift.");
            return;
        }

        // Function to trigger downloads one by one with a delay
        function triggerDownload(index) {
            if (index >= selectedGifts.length) return; // Stop when all downloads are triggered

            let giftId = selectedGifts[index];
            let downloadLink = document.querySelector(`a[data-gift-id='${giftId}']`);
            
            if (downloadLink) {
                let newWindow = window.open(downloadLink.href, '_blank'); // Open in a new tab
                if (newWindow) {
                    newWindow.blur(); // Prevent focusing the new tab
                    window.focus(); // Keep the main tab in focus
                }
            }

            // Trigger the next download after a short delay
            setTimeout(() => triggerDownload(index + 1), 1000); // Adjust delay if needed
        }

        triggerDownload(0); // Start the first download
    }

    function getSelectedGifts() {
        let selected = [];
        document.querySelectorAll(".gift-checkbox:checked").forEach(checkbox => {
            selected.push(checkbox.value);
        });
        return selected;
    }
</script>

@endsection
