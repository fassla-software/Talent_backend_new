@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="mb-3">Categories
        <button class="btn btn-success btn-sm" id="addCategoryBtn">Add Category</button>
    </h3>

    <!-- Search and Filter Form -->
    <form method="GET" action="{{ route('categories.index') }}" class="mb-3">
        <div class="row">
            <div class="col-md-6 mb-3">
                <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 mb-3">
                <select name="parent_id" class="form-control">
                    <option value="">Filter by Parent</option>
                    @foreach($dropdownCategories as $category)
                        <option value="{{ $category->id }}" {{ request('parent_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <select name="level" class="form-select">
                    <option value="">Filter by Level</option>
                    <option value="1" {{ request('level') == '1' ? 'selected' : '' }}>First</option>
                    <option value="2" {{ request('level') == '2' ? 'selected' : '' }}>Second</option>
                    <option value="3" {{ request('level') == '3' ? 'selected' : '' }}>Third</option>
                     <option value="4" {{ request('level') == '4' ? 'selected' : '' }}>Fourth</option>
                    <option value="5" {{ request('level') == '5' ? 'selected' : '' }}>Fifth</option>

                </select>
            </div>
            <div class="col-md-1 mb-3">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            <div class="col-md-1 mb-3">
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </div>
    </form>

    <!-- Bulk Delete Form -->
    <form id="bulkDeleteForm" method="POST" action="{{ route('categories.bulk_destroy') }}">
        @csrf
        @method('DELETE')

        <div class="mb-3">
            <button type="submit" class="btn btn-danger" id="bulkDeleteBtn" disabled onclick="return confirm('Are you sure?');">
                Delete Selected
            </button>
            <label class="ms-3">
                <input type="checkbox" id="selectAll"> Select All (Current Page)
            </label>
            <label class="ms-3">
                <input type="checkbox" id="selectAllDB"> Select All (Entire Database)
            </label>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="selectAllCheckboxes">
                    </th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Parent</th>
                    <th>Level</th>
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
                        <td>{{ $category->level }}</td>
                        <td>
                            <img src="{{ asset("plumber/uploads/" . $category->image) }}" class="img-thumbnail" style="width: 50px; height: 50px;">
                        </td>
                        <td>
                         <a href="#" class="btn btn-primary btn-sm edit-btn" 
   data-bs-toggle="modal" 
   data-bs-target="#editModal"
   data-id="{{ $category->id }}" 
   data-name="{{ $category->name }}" 
   data-parent_id="{{ $category->parent_id ?? '' }}"  {{-- ✅ Pass Parent ID or empty if NULL --}}
   data-points="{{ $category->points }}"
   onclick="event.stopPropagation();">
    Edit
</a>


                           
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <input type="hidden" name="delete_all" id="deleteAllInput" value="0">
    </form>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $categories->appends(request()->query())->links() }}
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const selectAllCheckbox = document.getElementById("selectAll");
    const selectAllDBCheckbox = document.getElementById("selectAllDB");
    const categoryCheckboxes = document.querySelectorAll(".categoryCheckbox");
    const bulkDeleteBtn = document.getElementById("bulkDeleteBtn");
    const deleteAllInput = document.getElementById("deleteAllInput");

    selectAllCheckbox.addEventListener("change", function() {
        categoryCheckboxes.forEach(checkbox => checkbox.checked = this.checked);
        toggleBulkDeleteButton();
    });

    function toggleBulkDeleteButton() {
        const anyChecked = Array.from(categoryCheckboxes).some(checkbox => checkbox.checked);
        bulkDeleteBtn.disabled = !anyChecked && !selectAllDBCheckbox.checked;
    }

    categoryCheckboxes.forEach(checkbox => checkbox.addEventListener("change", toggleBulkDeleteButton));

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





<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCategoryForm">
                    <div class="mb-3">
                        <label for="addName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="addName" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="addImage" class="form-label">Image</label>
                        <input type="file" class="form-control" id="addImage" name="image">
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select id="category" name="category_id" class="form-control">
                            <option value="">No Parent Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                @if (!empty($category['subcategories']))
                                    @foreach ($category['subcategories'] as $subcategory)
                                        <option value="{{ $subcategory['id'] }}">&nbsp;&nbsp;— {{ $subcategory['name'] }}</option>
                                        @if (!empty($subcategory['subcategories']))
                                            @foreach ($subcategory['subcategories'] as $subSubcategory)
                                                <option value="{{ $subSubcategory['id'] }}">&nbsp;&nbsp;&nbsp;&nbsp;— {{ $subSubcategory['name'] }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <!-- Items Flag Checkbox -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="addItemsFlag" name="itemsFlag">
                        <label class="form-check-label" for="addItemsFlag">Items Flag</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Edit Modal -->


<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="categoryId" name="id">

                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                    </div>
                    <div class="mb-3" style="display: none">
                        <label for="categoryPoints" class="form-label">Points</label>
                        <input type="number" class="form-control" id="categoryPoints" name="points" required>
                    </div>

                <div class="mb-3">
    <label for="editCategory" class="form-label">Parent Category</label>
    <select id="editCategory" name="parent_id" class="form-control">
        <option value="">No Parent Category</option>
        @foreach($dropdownCategories->where('product_flag', 0) as $category) 
            <option value="{{ $category->id }}">{{ $category->name }}</option>
            
            @if ($category->subcategories->where('product_flag', 0)->count() > 0)
                @foreach ($category->subcategories->where('product_flag', 0) as $subcategory)
                    <option value="{{ $subcategory->id }}">&nbsp;&nbsp;— {{ $subcategory->name }}</option>
                    
                    @if ($subcategory->subcategories->where('product_flag', 0)->count() > 0)
                        @foreach ($subcategory->subcategories->where('product_flag', 0) as $subSubcategory)
                            <option value="{{ $subSubcategory->id }}">&nbsp;&nbsp;&nbsp;&nbsp;— {{ $subSubcategory->name }}</option>

                            @if ($subSubcategory->subcategories->where('product_flag', 0)->count() > 0)
                                @foreach ($subSubcategory->subcategories->where('product_flag', 0) as $subSubSubcategory)
                                    <option value="{{ $subSubSubcategory->id }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;— {{ $subSubSubcategory->name }}</option>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach
    </select>
</div>



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
                                                 // Open Add Category Modal
// Open Add Category Modal
document.getElementById('addCategoryBtn').addEventListener('click', function () {
    $('#addCategoryModal').modal('show');
});

// Handle Add Category Form Submission
document.getElementById('addCategoryForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData();
    const imageInput = document.getElementById('addImage');

    // Get category name
    const categoryName = document.getElementById('addName').value.trim();
    if (!categoryName) {
        alert("Category name is required.");
        return;
    }

    // Get parent category ID, set to null if not selected
    const categoryId = document.getElementById('category').value || null;

    // Add the image file to FormData if selected
    if (imageInput.files.length > 0) {
        formData.append('images', imageInput.files[0]); // Upload image first
    }

    // Send the image file to the upload API
    fetch('https://app.talentindustrial.com/plumber/upload', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(imageResponse => {
        if (imageResponse.images && Array.isArray(imageResponse.images) && imageResponse.images.length > 0) {
            const imageUrl = imageResponse.images[0]; // Extract uploaded image URL

            const categoryData = {
                name: categoryName,
                image: imageUrl, // Use uploaded image URL
                points: 5,  // Default points (you can change this if necessary)
                parent_id: categoryId // Null if no parent selected
            };

            // Send the category data as JSON
            return fetch('https://app.talentindustrial.com/plumber/category', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(categoryData),
            });
        } else {
            throw new Error('Image upload failed. No valid image URL returned.');
        }
    })
    .then(response => response.json())
    .then(categoryResponse => {
        console.log('Category added successfully:', categoryResponse);

        if (categoryResponse.category) {
            console.log("Success: Category added with ID:", categoryResponse.category.id);
            alert("Category added successfully!");
        } else {
            console.error("Error: Failed to add category");
            alert("Failed to add category.");
        }

        // Close the modal and reload page to reflect changes
        $('#addCategoryModal').modal('hide');
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
});


 // Populate modal with category data
const editModal = document.getElementById('editModal');
editModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const parentId = button.getAttribute('data-parent_id'); // ✅ Parent ID

    const points = button.getAttribute('data-points');
    const imageUrl = button.getAttribute('data-image'); // Existing image

    // Set form values
    const form = document.getElementById('editForm');
    form.action = `https://app.talentindustrial.com/plumber/category/${id}`;
    document.getElementById('categoryId').value = id;
    document.getElementById('categoryName').value = name;
    document.getElementById('categoryPoints').value = points;
    const parentSelect = document.getElementById('editCategory'); // Make sure this is the correct ID
// Convert both to the same type (string) before comparison
    for (let i = 0; i < parentSelect.options.length; i++) {
        if (parentSelect.options[i].value === parentId || parseInt(parentSelect.options[i].value) === parseInt(parentId)) {
            parentSelect.options[i].selected = true;
            break;
        }
    }
    // Store existing values for reference
    form.dataset.originalName = name;
    form.dataset.originalPoints = points;
    form.dataset.originalImage = imageUrl;

    // Set existing image preview
    const existingImage = document.getElementById('existingImage');
    existingImage.src = imageUrl ? imageUrl : 'default-image.jpg'; // Use default if no image
});

// Handle form submission
document.getElementById('editForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent default form submission

    const form = e.target;
    const url = form.action;
    const formData = new FormData(form);
    const imageInput = document.getElementById('editImage');

    // Get original values
    const originalName = form.dataset.originalName;
    const originalPoints = form.dataset.originalPoints;
    const originalImage = form.dataset.originalImage;

    // Check for changes
    const newName = formData.get('name').trim() || originalName;
    const newPoints = formData.get('points').trim() || originalPoints;

    if (imageInput.files.length > 0) {
        const imageFormData = new FormData();
        imageFormData.append('images', imageInput.files[0]); // Upload new image

        fetch('https://app.talentindustrial.com/plumber/upload', {
            method: 'POST',
            body: imageFormData,
        })
        .then(response => response.json())
        .then(imageResponse => {
            if (imageResponse.images && Array.isArray(imageResponse.images) && imageResponse.images.length > 0) {
                const uploadedImageUrl = imageResponse.images[0]; // Get uploaded image URL
                updateCategory(url, newName, newPoints, uploadedImageUrl);
            } else {
                throw new Error('Image upload failed. No valid image URL returned.');
            }
        })
        .catch(error => {
            console.error('Image Upload Error:', error);
            alert('Failed to upload image. Please try again.');
        });
    } else {
        updateCategory(url, newName, newPoints, originalImage);
    }
});

// Function to update category
function updateCategory(url, name, points, imageUrl) {
    const requestData = {
        name: name,
        points: 0,
        image: imageUrl,
    };

    fetch(url, {
        method: 'PUT',
        body: JSON.stringify(requestData),
        headers: {
            'Content-Type': 'application/json',
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
        location.reload(); // Reload page to reflect changes
    })
    .catch(error => {
        console.error('Error updating category:', error);
        alert('Failed to update the category. Please try again.');
    });
}


</script>

<style>
    .modal-content {
        border-radius: 12px;
        overflow: hidden;
    }

    .modal-header, .modal-footer {
        border: 0;
        background-color: #ffffff;
    }

    .modal-body {
        font-size: 1.1rem;
    }

    .modal-body .text-muted {
        font-weight: bold;
        color: #6c757d !important;
    }

    .modal-footer {
        border-top: 1px solid #ddd;
    }

    .expand-arrow {
        font-size: 1.5rem;
        color: #007bff;
    }

    .sub-category-list {
        display: none;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .sub-category-list.expanded {
        display: block;
        animation: expand 0.5s ease-out;
    }

    @keyframes expand {
        from {
            opacity: 0;
            height: 0;
        }
        to {
            opacity: 1;
            height: auto;
        }
    }
</style>
@endsection
