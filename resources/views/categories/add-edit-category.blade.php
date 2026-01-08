@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">Categories 
        <button class="btn btn-success btn-sm" id="addCategoryBtn">Add Category</button>
    </h3>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Items Flag</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <!-- Display main category -->
                <tr>
                    <td>{{ $category['id'] }}</td>
                    <td>{{ $category['name'] }}</td>
                    <td>{{ $category['itemsFlag'] ? 'Yes' : 'No' }}</td>
                    <td>
                        <button class="btn btn-success btn-sm edit-btn" data-id="{{ $category['id'] }}" data-name="{{ $category['name'] }}" data-points="{{ $category['points'] }}" data-itemsflag="{{ $category['itemsFlag'] }}"><i class="fa fa-edit"></i></button>
                                   <form method="POST" class="delete-form-category"  style="display: inline"  data-route="{{route('add-edit-category.destroy', $category['id'])}}">
										@method('DELETE')
										@csrf

                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                    </td>
                </tr>

                <!-- Recursively display subcategories -->
                @foreach($category['subcategories'] as $subcategory)
                    <tr>
                        <td>{{ $subcategory['id'] }}</td>
                        <td>{{ $subcategory['name'] }}</td>
                        <td>{{ $subcategory['itemsFlag'] ? 'Yes' : 'No' }}</td>
                        <td>
                            <button class="btn btn-success btn-sm edit-btn" data-id="{{ $subcategory['id'] }}" data-name="{{ $subcategory['name'] }}" data-points="{{ $subcategory['points'] }}" data-itemsflag="{{ $subcategory['itemsFlag'] }}"><i class="fa fa-edit"></i></button>
                                   <form method="POST" class="delete-form-category"  style="display: inline"  data-route="{{route('add-edit-category.destroy', $subcategory['id'])}}">
										@method('DELETE')
										@csrf

                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                        </td>
                    </tr>

                    <!-- Recursively display sub-subcategories -->
                    @foreach($subcategory['subcategories'] as $subSubcategory)
                        <tr>
                            <td>{{ $subSubcategory['id'] }}</td>
                            <td>{{ $subSubcategory['name'] }}</td>
                            <td>{{ $subSubcategory['itemsFlag'] ? 'Yes' : 'No' }}</td>
                            <td>
                                <button class="btn btn-success btn-sm edit-btn" data-id="{{ $subSubcategory['id'] }}" data-name="{{ $subSubcategory['name'] }}" data-points="{{ $subSubcategory['points'] }}" data-itemsflag="{{ $subSubcategory['itemsFlag'] }}"><i class="fa fa-edit"></i></button>
                                   <form method="POST" class="delete-form-category"  style="display: inline"  data-route="{{route('add-edit-category.destroy', $subSubcategory['id'])}}">
										@method('DELETE')
										@csrf

                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>

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
                            <option value="">Select a Category</option>
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
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editName" name="name">
                    </div>
                    <!-- <div class="mb-3"> -->
                       <!-- <label for="editPoints" class="form-label">Points</label> -->
                       <!-- <input type="number" class="form-control" id="editPoints" name="points"> -->
                   <!-- </div> -->
                    <div class="mb-3">
                        <label for="editItemsFlag" class="form-label">Items Flag</label>
                        <input type="checkbox" id="editItemsFlag" name="itemsFlag">
                    </div>
                    <input type="hidden" id="editId" name="id">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Open Add Category Modal
document.getElementById('addCategoryBtn').addEventListener('click', function () {
    $('#addCategoryModal').modal('show');
});

// Handle Add Category Form Submission
document.getElementById('addCategoryForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData();
    const imageInput = document.getElementById('addImage');

    // Get the category_id, set it to null if no category is selected
    const categoryId = document.getElementById('category').value || null;

    // Add the image file to FormData if selected
    if (imageInput.files.length > 0) {
        formData.append('images', imageInput.files[0]); // Use the 'images' key as required by the API
    }

    // Send the image file to the upload API
    fetch('https://app.talentindustrial.com/plumber/upload', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(imageResponse => {
        if (imageResponse.images && Array.isArray(imageResponse.images) && imageResponse.images.length > 0) {
            const imageUrl = imageResponse.images[0]; // Get the first image from the array

            const categoryData = {
                name: document.getElementById('addName').value,
                image: imageUrl, // Use the returned image URL
                points: 0,  // Assuming points are required, add default or get from the input
                category_id: categoryId, // This will be null if no category is selected
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

        // Close the modal
        $('#addCategoryModal').modal('hide');
        // Reload the page or update the table with the new data
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
});





    // Listen for click on "Edit" buttons
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            // const points = this.getAttribute('data-points');
            const itemsFlag = this.getAttribute('data-itemsflag');

            // Set form data in the modal
            document.getElementById('editName').value = name;
            // document.getElementById('editPoints').value = points;
            document.getElementById('editItemsFlag').checked = itemsFlag;
            document.getElementById('editId').value = id;

            // Show the modal
            $('#editCategoryModal').modal('show');
        });
    });

    // Handle the form submission for editing a category
    document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const id = document.getElementById('editId').value;
        const name = document.getElementById('editName').value;
        const points = document.getElementById('editPoints').value;
        const itemsFlag = document.getElementById('editItemsFlag').checked;

        const data = {
            name: name,
            points: points,
            itemsFlag: itemsFlag
        };

        fetch(`https://app.talentindustrial.com/plumber/category/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            // Close the modal
            $('#editCategoryModal').modal('hide');
            // Reload the page or update the table with the new data
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
// Open Add Category Modal when clicking on "Open Add Category Modal" button
    // document.getElementById('openAddCategoryModalBtn').addEventListener('click', function () {
    //     $('#addCategoryModal').modal('show');
    // });

</script>


@endsection
