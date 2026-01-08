@extends('layouts.app')

@section('title', 'Create Level')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-7 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Create New Level</h1>
                                </div>

                                <form action="{{ route('level.store') }}" method="POST" class="user">
                                    @csrf

                                    <div class="form-group">
                                        <label for="level" class="form-label">Level Number</label>
                                        <input type="number" name="level" id="level" value="{{ old('level') }}"
                                               class="form-control form-control-user @error('level') is-invalid @enderror"
                                               placeholder="Enter level number" required>
                                        @error('level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="min_sales" class="form-label">Minimum Coupons</label>
                                        <input type="number" name="min_sales" id="min_sales" value="{{ old('min_sales') }}"
                                               class="form-control form-control-user @error('min_sales') is-invalid @enderror"
                                               placeholder="Enter minimum sales" required>
                                        @error('min_sales')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="max_sales" class="form-label">Maximum Coupons</label>
                                        <input type="number" name="max_sales" id="max_sales" value="{{ old('max_sales') }}"
                                               class="form-control form-control-user @error('max_sales') is-invalid @enderror"
                                               placeholder="Enter maximum sales" required>
                                        @error('max_sales')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="points" class="form-label">Value</label>
                                        <input type="number" name="points" id="points" value="{{ old('points') }}"
                                               class="form-control form-control-user @error('points') is-invalid @enderror"
                                               placeholder="Enter points" required>
                                        @error('points')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group d-flex justify-content-between mt-10">
                                        <a href="{{ route('level.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Back to Levels
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Create Level
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
