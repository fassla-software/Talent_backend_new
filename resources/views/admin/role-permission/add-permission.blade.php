@extends('layouts.app')

@section('title', 'Add New Permission')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4>Add New Permission</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.permission.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Permission Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Permission Name" required>
                </div>

                <button type="submit" class="btn btn-primary">Add Permission</button>
                <a href="{{ route('admin.role.index') }}" class="btn btn-secondary">Back to Roles</a>
            </form>
        </div>
    </div>

    @if($permissions->count())
    <div class="card mt-4">
        <div class="card-header">
            <h4>Existing Permissions</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Permission Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $index => $permission)
                        <tr>
                            <td>{{ $permissions->firstItem() + $index }}</td>
                            <td>{{ $permission->name }}</td>
                          <td>
    <form action="{{ route('admin.permission.delete', $permission->id) }}" method="POST" id="delete-form-{{ $permission->id }}">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $permission->id }})">Delete</button>
    </form>
</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center mt-3">
                {{ $permissions->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
            
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <script>
    function confirmDelete(permissionId) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("delete-form-" + permissionId).submit();
            }
        });
    }
</script>



@endsection
