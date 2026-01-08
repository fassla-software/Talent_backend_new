@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Photo Gallery</h2>

    <!-- Upload Form -->
    <form action="{{ route('gallery.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <input type="file" name="images[]" class="form-control" multiple required> <!-- Allow multiple file selection -->
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>

    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif

<!-- Delete All Button -->
<form id="deleteAllForm" action="{{ route('gallery.deleteAll') }}" method="POST" class="mb-3">
    @csrf
    @method('DELETE')
    <button type="button" class="btn btn-danger" onclick="confirmDeleteAll()">Delete All Images</button>
</form>

<!-- JavaScript for Confirmation -->
<script>
    function confirmDeleteAll() {
        if (confirm("Are you sure you want to delete all images? This action cannot be undone!")) {
            document.getElementById('deleteAllForm').submit();
        }
    }
</script>


<a href="{{ route('gallery.export') }}" class="btn btn-success">Export to Excel</a>

   <!-- Display All Images -->
<div class="row mt-4">
    @foreach($images as $image)
    <div class="col-md-2">
        <div class="card shadow-sm border-0 mb-3">
            <img src="{{ $image->url }}" class="card-img-top rounded" alt="{{ $image->filename }}">
            <div class="card-body text-center">
                <h6 class="text-truncate">{{ basename($image->url) }}</h6> <!-- Extract filename -->
                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('{{ route('gallery.delete', $image->id) }}')">Delete</button>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination Controls -->
<div class="d-flex justify-content-center mt-4">
    {{ $images->links() }} <!-- Generates pagination links -->
</div>


<!-- Bootstrap Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this image?
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(deleteUrl) {
        document.getElementById('deleteForm').action = deleteUrl;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>

@endsection
