@extends('layouts.app')

@section('title', 'Level Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Level Details</h1>
                <div>
                    <a href="{{ route('level.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left"></i> Back to Levels
                    </a>
                    <a href="{{ route('level.edit', $level) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Level
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Level Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">ID</label>
                                <p class="form-control-plaintext">{{ $level->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Level</label>
                                <p class="form-control-plaintext">{{ $level->level }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Min Sales</label>
                                <p class="form-control-plaintext">{{ number_format($level->min_sales) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Max Sales</label>
                                <p class="form-control-plaintext">{{ number_format($level->max_sales) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Points</label>
                                <p class="form-control-plaintext">{{ number_format($level->points) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
