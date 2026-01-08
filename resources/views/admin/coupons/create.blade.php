@extends('layouts.app')

@section('content')
<div class="container mt-4">

    {{-- ===== Page Header ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-semibold  mb-0"> {{ __('Add New Distributor Coupon') }}</h3>
    </div>

    {{-- ===== Coupon Form ===== --}}
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <form action="{{ route('admin.coupons.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-primary text-white py-3 rounded-top">
                        <h5 class="m-0"><i class="bi bi-plus-circle me-2"></i>{{ __('Add New Distributor Coupon') }}</h5>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            {{-- Distributor --}}
                            <div class="col-md-6">
                                <x-select name="distributor_id" label="{{ __('Select Distributor') }}" required="true">
                                    <option value="">{{ __('Choose Distributor') }}</option>
                                    @foreach ($distributors as $distributor)
                                        <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                                    @endforeach
                                </x-select>
                            </div>

                            {{-- Sales Value --}}
                            <div class="col-md-6">
                                <x-input name="sales_value" type="number" min="0" placeholder="Invoice Value"
                                    label="Invoice Value" required="true" />
                            </div>

                            {{-- Area --}}
                            <div class="col-md-6">
                                <x-select name="area_name" label="{{ __('Select Area') }}" required="true">
                                    <option value="">{{ __('Choose Area') }}</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area }}">{{ $area }}</option>
                                    @endforeach
                                </x-select>
                            </div>

                            {{-- Base Amount --}}
                            <div class="col-md-6">
                                <x-input name="base_amount" type="number" min="0" placeholder="Base Value"
                                    label="Base Amount Value" required="true" />
                            </div>

                            {{-- Points --}}
                            <div class="col-md-6">
                                <x-input name="points" type="number" min="0" placeholder="Coupon Value"
                                    label="Refund Value" required="true" />
                            </div>

                            {{-- Expired Date --}}
                            <div class="col-md-6">
                                <x-input type="text" id="datepicker" label="Expired Date" name="expired_at"
                                    required="true" placeholder="mm/dd/yyyy" />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light py-3 d-flex justify-content-end gap-3 rounded-bottom">
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-x-circle"></i> {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-check-circle"></i> {{ __('Submit') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== Custom Styles ===== --}}
<style>
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .card-header {
        border-bottom: 0;
    }

    .form-control:focus, select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25);
    }

    label {
        font-weight: 500;
        color: #444;
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        $("#datepicker").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: "mm/dd/yy",
            minDate: 0
        });
    });
</script>
@endpush
@endsection
