@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">Add a New Product</h3>

    <form id="addProductForm">
        @csrf

        <!-- Product Name -->
        <div class="mb-3">
            <label for="productName" class="form-label">Product Name</label>
            <input type="text" id="productName" name="name" class="form-control" required>
        </div>

        <!-- Image Upload -->
        <div class="mb-3">
            <label for="productImage" class="form-label">Image</label>
            <input type="file" id="productImage" class="form-control" accept="image/*" required>
        </div>

        <!-- Category Dropdown -->
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select id="category" name="category_id" class="form-control" required>
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

        <!-- Points -->
        <div class="mb-3">
            <label for="points" class="form-label">Points</label>
            <input type="number" id="points" name="points" class="form-control" step="0.01" required>
        </div>

        <!-- Hidden Product Flag (Always Checked) -->
        <input type="hidden" id="productFlag" name="product_flag" value="1">

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Submit Product</button>
    </form>
</div>

<script>
    document.getElementById('addProductForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData();
        const imageInput = document.getElementById('productImage');

        if (imageInput.files.length > 0) {
            formData.append('images', imageInput.files[0]); // API expects key name 'images'
        }

        fetch('https://app.talentindustrial.com/plumber/upload', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(imageResponse => {
            if (imageResponse.images && Array.isArray(imageResponse.images) && imageResponse.images.length > 0) {
                const imageUrl = imageResponse.images[0];

                const productData = {
                    name: document.getElementById('productName').value,
                    image: imageUrl,
                    category_id: document.getElementById('category').value,
                    points: document.getElementById('points').value,
                    product_flag: 1, // Always save as checked
                };

                return fetch("{{ route('flagged.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(productData),
                });
            } else {
                throw new Error('Image upload failed. Please try again.');
            }
        })
        .then(response => response.json())
        .then(productResponse => {
            alert('Product added successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to add product: ' + error.message);
        });
    });
</script>

@endsection
