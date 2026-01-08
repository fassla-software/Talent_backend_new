@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Upload Categories</h1>

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

        <!-- Button to download the template -->
<a href="{{ route('downloadTemplate') }}" class="btn btn-info mb-3">Download Template</a>

        <form action="{{ route('plumber_categories.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">Upload Excel File</label>
                <input type="file" class="form-control" id="file" name="file" accept=".xls,.xlsx" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
@endsection
