@extends('layouts.app')

@section('title', 'Edit Plumber')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Edit Plumber</h1>
                <div>
                    <a href="{{ route('admin.plumberUsers.show', $plumber->id) }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    <a href="{{ route('admin.plumberUsers') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Plumbers
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Plumber Information</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.plumberUsers.update', $plumber->user_id) }}">
                        @csrf
                        @method('patch')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Name</label>
                                    <input type="text" name="name" value="{{ $plumber->user->name }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Phone</label>
                                    <input type="text" name="phone" value="{{ $plumber->user->phone }}" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Gift Points</label>
                                    <input type="number" min="0" name="gift_points" value="{{ $plumber->gift_points }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Fixed Points</label>
                                    <input type="number" min="0" name="fixed_points" value="{{ $plumber->fixed_points }}" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Nationality ID</label>
                                    <input
                                        type="text"
                                        name="nationality_id"
                                        value="{{ $plumber->nationality_id }}"
                                        class="form-control"
                                        pattern="\d{14}"
                                        maxlength="14"
                                        minlength="14"
                                        inputmode="numeric"
                                        title="Nationality ID must be exactly 14 digits"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="{{ route('admin.plumberUsers.show', $plumber->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
