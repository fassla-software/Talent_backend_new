@extends('layouts.app')

@section('content')
<div class="container">
    <h1>All Gifts</h1>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

        
<form method="GET" action="{{ url()->current() }}" class="mb-3">
    <div class="input-group">
        <input
            type="text"
            name="name"
            class="form-control"
            placeholder="Search by name"
            value="{{ request('name') }}"
        >
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>

        
        
    @if (!empty($gifts))
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Points Required</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Action</th> <!-- Added Action column -->
                </tr>
            </thead>
            <tbody>
                @foreach ($gifts as $index => $gift)
                    <tr>
                        <td>{{ (int)$index + 1 }}</td>
                        <td>{{ $gift['name'] }}</td>
                        <td><img src="{{ $gift['image'] }}" alt="{{ $gift['name'] }}" width="100"></td>
                        <td>{{ $gift['points_required'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($gift['createdAt'])->format('Y-m-d H:i:s') }}</td>
                        <td>{{ \Carbon\Carbon::parse($gift['updatedAt'])->format('Y-m-d H:i:s') }}</td>
                        <td>
							<button style="font-size: 1rem;" class="btn btn-primary btn-sm" onclick="updateGift({{ $gift['id'] }}, '{{ addslashes($gift['name']) }}', {{ $gift['points_required'] ?? 'null' }}, '{{ $gift['image'] ?? '' }}')"><i class="fa fa-edit"></i></button>

        @hasPermission('plumber.gift.delete')

                                    <form method="POST" class="delete-form-gift"  style="display: inline"  data-route="{{route('gift.destroy', $gift['id'])}}">
										@method('DELETE')
										@csrf

                                        <button style="font-size: 1rem;" type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
@endhasPermission

<script>
    function updateGift(id, currentName, currentPointsRequired, currentImage) {
        // Step 1: Prompt for the name with the existing value
        Swal.fire({
            title: 'Update Gift Name',
            input: 'text',
            inputLabel: 'Enter the new name (optional)',
            inputValue: currentName || '',  // Pre-fill with current gift name
            inputPlaceholder: 'Gift name',
            showCancelButton: true,
            confirmButtonText: 'Next',
        }).then((nameResult) => {
            if (nameResult.isConfirmed) {
                const name = nameResult.value || null;

                // Step 2: Prompt for points required with the existing value
                Swal.fire({
                    title: 'Update Points Required',
                    input: 'number',
                    inputLabel: 'Enter the points required (optional)',
                    inputValue: currentPointsRequired || '',  // Pre-fill with current points_required
                    inputPlaceholder: 'Points required',
                    showCancelButton: true,
                    confirmButtonText: 'Next',
                }).then((pointsResult) => {
                    const pointsRequired = pointsResult.isConfirmed ? pointsResult.value : null;

                    // Step 3: Prompt for image upload
                    Swal.fire({
                        title: 'Upload Image',
                        html: '<input type="file" id="imageInput" accept="image/*" class="swal2-file">',
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Upload',
                        preConfirm: () => {
                            const fileInput = Swal.getPopup().querySelector('#imageInput');
                            return fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
                        },
                    }).then((imageResult) => {
                        const imageFile = imageResult.isConfirmed ? imageResult.value : null;

                        if (imageFile) {
                            // Upload the image
                            const formData = new FormData();
                            formData.append('images', imageFile);

                            $.ajax({
                                type: 'POST',
                                url: 'https://app.talentindustrial.com/plumber/upload',
                                data: formData,
                                contentType: false,
                                processData: false,
                                success: function (uploadResponse) {
                                    const uploadedImageUrl = uploadResponse.images[0];
                                    updateGiftData(id, name, pointsRequired, uploadedImageUrl);
                                },
                                error: function () {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Upload Failed',
                                        text: 'Failed to upload the image. Please try again.',
                                    });
                                }
                            });
                        } else {
                            // No image uploaded, proceed with existing data
                            updateGiftData(id, name, pointsRequired, currentImage);
                        }
                    });
                });
            }
        });
    }

    function updateGiftData(id, name, pointsRequired, imageUrl) {
        const data = {};
        if (name !== null && name !== undefined) data.name = name;
        if (pointsRequired !== null && pointsRequired !== undefined) data.points_required = pointsRequired;
        if (imageUrl !== null && imageUrl !== undefined) data.image = imageUrl;

        $.ajax({
            type: 'PUT',  
            url: 'https://app.talentindustrial.com/plumber/gift/' + id,
            data: data,
            success: function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Gift Updated',
                    text: 'The gift was updated successfully!',
                    confirmButtonText: 'OK',
                }).then(() => {
                    location.reload();
                });
            },
            error: function (xhr) {
                const response = xhr.responseJSON || {};
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: response.message || 'Failed to update the gift. Please try again.',
                });
            },
        });
    }
</script>

                      </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No gifts available.</p>
    @endif
</div>

<!-- Pagination Links -->
        {{ $gifts->links() }}

@section('styles')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>
@endsection

@endsection
