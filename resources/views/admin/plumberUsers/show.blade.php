@extends('layouts.app')

@section('title', 'Plumber Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Plumber Details</h1>
                <div>
                    <a href="{{ route('admin.plumberUsers') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-arrow-left"></i> Back to Plumbers
                    </a>
                    <a href="{{ route('admin.plumberUsers.edit', $plumber->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Plumber
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Plumber Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                <label class="font-weight-bolder">ID</label>
                                <p class="form-control-plaintext">{{ $plumber->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                <label class="font-weight-bolder">Name</label>
                                <p class="form-control-plaintext">{{ $plumber->user->name ?? 'No User' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                <label class="font-weight-bolder">Phone</label>
                                <p class="form-control-plaintext">{{ $plumber->user->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                <label class="font-weight-bolder">City</label>
                                <p class="form-control-plaintext">{{ $plumber->city ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                <label class="font-weight-bolder">Area</label>
                                <p class="form-control-plaintext">{{ $plumber->area ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                <label class="font-weight-bolder">Nationality ID</label>
                                <p class="form-control-plaintext">{{ $plumber->nationality_id ?? 'No Nationality' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                <label class="font-weight-bolder">Verified?</label>
                                <p class="form-control-plaintext">{{ isset($plumber->is_verified) && $plumber->is_verified ? 'Yes' : 'No' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                <label class="font-weight-bolder">Instant Withdrawal</label>
                                <p class="form-control-plaintext">{{ $plumber->instant_withdrawal }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                <label class="font-weight-bolder">Withdraw Money</label>
                                <p class="form-control-plaintext">{{ $plumber->withdraw_money }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                <label class="font-weight-bolder">Gift Points</label>
                                <p class="form-control-plaintext">{{ $plumber->gift_points }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                <label class="font-weight-bolder">Fixed Points</label>
                                <p class="form-control-plaintext">{{ $plumber->fixed_points }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                <label class="font-weight-bolder">Created At</label>
                                <p class="form-control-plaintext">{{ \Carbon\Carbon::parse($plumber->created_at)->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-3 p-2 border rounded bg-light">
                                <label class="font-weight-bolder">Updated At</label>
                                <p class="form-control-plaintext">{{ \Carbon\Carbon::parse($plumber->updated_at)->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Images</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Nationality Image 1</label>
                                @if($plumber->nationality_image1)
                                    <img src="https://app.talentindustrial.com/plumber/uploads/{{ $plumber->nationality_image1 }}" alt="Nationality Image 1" class="img-fluid" style="max-height: 200px;">
                                @else
                                    <p class="form-control-plaintext">No Image Available</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Nationality Image 2</label>
                                @if($plumber->nationality_image2)
                                    <img src="https://app.talentindustrial.com/plumber/uploads/{{ $plumber->nationality_image2 }}" alt="Nationality Image 2" class="img-fluid" style="max-height: 200px;">
                                @else
                                    <p class="form-control-plaintext">No Image Available</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Plumber Image</label>
                                @if($plumber->image)
                                    <img src="https://app.talentindustrial.com/plumber/uploads/{{ $plumber->image }}" alt="Plumber Image" class="img-fluid" style="max-height: 200px;">
                                @else
                                    <p class="form-control-plaintext">No Image Available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
