@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">Products</h3>

    <form method="GET" action="{{ route('flagged.index') }}" class="row mb-4">
        <!-- Search by Name -->
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by Name">
        </div>

        <!-- Filter by Points -->
        <div class="col-md-3">
            <input type="number" name="points" class="form-control" value="{{ request('points') }}" placeholder="Filter by Points">
        </div>

        <!-- Filter by Category -->
        <div class="col-md-3 mb-3">
            <select name="parent_id" class="form-control">
                <option value="">Filter by Parent</option>
                @foreach($dropdownCategories as $cat)
                    <option value="{{ $cat->id }}" {{ request('parent_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </div>
    </form>

    <!-- Bulk Delete Form -->
    <form id="bulkDeleteForm" method="POST" action="{{ route('plumber_categories.bulk_destroy') }}">
        @csrf
        @method('DELETE')

        <div class="mb-3">
            <!-- Bulk Delete Button -->
            <button type="submit" class="btn btn-danger" id="bulkDeleteBtn" disabled
                    onclick="return confirm('Are you sure you want to delete selected categories?');">
                Delete Selected
            </button>

            <!-- Select All (Visible) -->
            <label class="ms-3">
                <input type="checkbox" id="selectAll"> Select All (Current Page)
            </label>

            <!-- Select All in Database -->
            <label class="ms-3">
                <input type="checkbox" id="selectAllDB"> Select All (Entire Database)
            </label>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="selectAllCheckboxes">
                    </th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Parent</th>
                    <th>Points</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" class="categoryCheckbox">
                        </td>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->parent ? $category->parent->name : 'No Parent' }}</td>
                        <td>{{ $category->points }}</td>
                        <td>
                            <img src="{{ asset("plumber/uploads/" . $category->image) }}" alt="Category Image"
                                 class="img-thumbnail" style="width: 50px; height: 50px;">
                        </td>
                        <td>
                            <a href="#" class="btn btn-primary btn-sm edit-btn"
                               data-bs-toggle="modal"
                               data-bs-target="#editModal"
                               data-id="{{ $category->id }}"
                               data-name="{{ $category->name }}"
                               data-points="{{ $category->points }}"
                               data-parent_id="{{ $category->parent_id }}"
                               data-image="{{ $category->image }}"
                               onclick="event.stopPropagation();">
                               Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Hidden input to mark "delete all in database" -->
        <input type="hidden" name="delete_all" id="deleteAllInput" value="0">
    </form>

    <!-- Pagination Links -->
    <div class="pagination">
        {{ $categories->links() }}
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const selectAllCheckbox = document.getElementById("selectAll");
    const selectAllDBCheckbox = document.getElementById("selectAllDB");
    const categoryCheckboxes = document.querySelectorAll(".categoryCheckbox");
    const bulkDeleteBtn = document.getElementById("bulkDeleteBtn");
    const deleteAllInput = document.getElementById("deleteAllInput");

    // Toggle all checkboxes (current page)
    selectAllCheckbox.addEventListener("change", function() {
        categoryCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleBulkDeleteButton();
    });

    // Enable bulk delete when at least one is selected
    function toggleBulkDeleteButton() {
        const anyChecked = Array.from(categoryCheckboxes).some(checkbox => checkbox.checked);
        bulkDeleteBtn.disabled = !anyChecked && !selectAllDBCheckbox.checked;
    }

    // Handle individual checkbox selection
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener("change", toggleBulkDeleteButton);
    });

    // "Select All in Database" Checkbox
    selectAllDBCheckbox.addEventListener("change", function() {
        deleteAllInput.value = this.checked ? "1" : "0";
        bulkDeleteBtn.disabled = !this.checked;

        if (this.checked) {
            selectAllCheckbox.checked = false;
            categoryCheckboxes.forEach(checkbox => checkbox.checked = false);
        }
    });
});
</script>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <!-- Hidden category ID -->
                    <input type="hidden" id="categoryId" name="id">

                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="categoryPoints" class="form-label">Points</label>
                        <input type="number" class="form-control" id="categoryPoints" name="points" step="0.01" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select id="category" name="category_id" class="form-control">
                            <option value="">Select a Category</option>
                            @foreach($dropdownCategories as $cat)
                                @include('components.category-option', ['category' => $cat, 'prefix' => ''])
                            @endforeach
                        </select>
                    </div>

                    <!-- Current Image Preview 
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <br>
                        <img id="existingImage" src="" alt="Current Image" class="img-thumbnail" style="width: 100px; height: 100px;">
                    </div>
 -->
                    <!-- New Image Upload -->
                    <div class="mb-3">
                        <label for="editImage" class="form-label">Change Image</label>
                        <input type="file" class="form-control" id="editImage" name="image">
                    </div>

                    <button type="submit" class="btn btn-success">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
////////////////////////////////////////////////////////////////////////////////
// 1) Listen for the modal show event, populate fields, including existing image
////////////////////////////////////////////////////////////////////////////////
document.addEventListener('DOMContentLoaded', () => {
    const editModal = document.getElementById('editModal');
    const editForm  = document.getElementById('editForm');
    const existingImage = document.getElementById('existingImage');

    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        // Extract data from the button
        const id         = button.getAttribute('data-id');
        const name       = button.getAttribute('data-name');
        const points     = button.getAttribute('data-points');
        const parentId   = button.getAttribute('data-parent_id');
        const imageUrl   = button.getAttribute('data-image');

        console.log('EDIT MODAL TRIGGERED =>', { id, name, points, parentId, imageUrl });

        // Populate the hidden input & text fields
        document.getElementById('categoryId').value     = id;
        document.getElementById('categoryName').value   = name;
        document.getElementById('categoryPoints').value = points;

        // Pre-select the category dropdown
        const categorySelect = document.getElementById('category');
        categorySelect.value = parentId || '';

        // Set existing image preview or default
        existingImage.src = imageUrl ? `{{ asset('plumber/uploads/') }}/${imageUrl}` : 'default-image.jpg';

        // Use local route or whichever route you want to handle the update
        // to process the final PUT request
        editForm.action = `{{ url('/flagged-categories/update') }}/${id}`;
    });

    ////////////////////////////////////////////////////////////////////////////////
    // 2) Intercept the form submission, handle image upload, then call update
    ////////////////////////////////////////////////////////////////////////////////
    editForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent normal form submission

        const id         = document.getElementById('categoryId').value;
        const formData   = new FormData(editForm);
        const imageInput = document.getElementById('editImage');

        // Build the final route for your update
        const finalUrl   = `{{ url('/flagged-categories/update') }}/${id}`;

        // If a new image was uploaded
        if (imageInput.files.length > 0) {
            const imageFormData = new FormData();
            imageFormData.append('images', imageInput.files[0]);

            // 2A) Upload the image to external API first
            fetch('https://app.talentindustrial.com/plumber/upload', {
                method: 'POST',
                body: imageFormData,
            })
            .then(response => response.json())
            .then(imageResponse => {
                if (imageResponse.images && Array.isArray(imageResponse.images) && imageResponse.images.length > 0) {
                    const uploadedImageUrl = imageResponse.images[0]; // The new image path
                    updateCategory(finalUrl, formData, uploadedImageUrl);
                } else {
                    throw new Error('Image upload failed. No valid image URL returned.');
                }
            })
            .catch(error => {
                console.error('Image Upload Error:', error);
                alert('Failed to upload image. Please try again.');
            });
        } else {
            // 2B) No new image, just update category with existing data
            updateCategory(finalUrl, formData, null);
        }
    });

    ////////////////////////////////////////////////////////////////////////////////
    // 3) The function that calls your local Laravel PUT route with final data
    ////////////////////////////////////////////////////////////////////////////////
    function updateCategory(url, originalFormData, uploadedImageUrl) {
        // Gather your field data from the original FormData
        const requestData = {
            name:       originalFormData.get('name'),
            points:     originalFormData.get('points'),
            category_id: originalFormData.get('category_id'),
        };

        // If a new image was uploaded, add it to the request
        if (uploadedImageUrl) {
            requestData.image = uploadedImageUrl;
        }

        fetch(url, {
            method: 'PUT',
            body: JSON.stringify(requestData),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            alert('Category updated successfully!');
            location.reload(); // Refresh the page to see changes
        })
        .catch(error => {
            console.error('Error updating category:', error);
            alert('Failed to update the category. Please try again.');
        });
    }
});
</script>
@endsection
