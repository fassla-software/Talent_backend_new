@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create a New Gift</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('gift.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Gift Name:</label>
        <input type="text" id="name" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Image:</label>
        <input type="file" id="image" name="image" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="points_required" class="form-label">Points Required:</label>
        <input type="number" id="points_required" name="points_required" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Create Gift</button>
</form>

</div>
@endsection
