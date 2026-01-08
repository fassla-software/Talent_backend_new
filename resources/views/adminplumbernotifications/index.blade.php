@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Admin Notifications</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Message</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notifications as $notification)
                <tr class="{{ $notification->read ? 'text-muted' : 'table-warning' }}"> 
                    <td>{{ $notification->title }}</td>
                    <td>{{ $notification->message }}</td>
                    <td>
                        @if($notification->read)
                            <span class="badge bg-secondary">Read</span>
                        @else
                            <span class="badge bg-danger">Unread</span>
                        @endif
                    </td>
                    <td>
                        @if(!$notification->read)
                            <button class="btn btn-success btn-sm mark-as-read" data-id="{{ $notification->id }}">Mark as Read</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ✅ Pagination Links -->
    <div class="d-flex justify-content-center mt-3">
        {{ $notifications->links() }}
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".mark-as-read").forEach(button => {
            button.addEventListener("click", function() {
                let notificationId = this.getAttribute("data-id");

                fetch(`/adminplumbernotifications/read/${notificationId}`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        "Content-Type": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload(); // ✅ Refresh page to update notification status
                })
                .catch(error => console.error("Error:", error));
            });
        });
    });
</script>

@endsection
