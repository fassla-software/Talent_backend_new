@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Coupon Labels</h4>
        <button onclick="window.print()" class="btn btn-primary">Print Labels</button>
    </div>

    @if(count($data) > 0)
        <div class="row">
            @foreach($data as $coupon)
                <div class="col-6 col-md-3 mb-4">
                    <div class="card text-center p-2 shadow-sm">
                        <div class=" row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-6">
                                        <img src="{{ $coupon['qr'] }}" alt="QR Code">
                                   </div>
                                    <div class="col-6">
                                        <h6 class="fw-bold">{{ $coupon['code'] }}</h6>
                                        <p style="font-size: 12px; margin:0;">{{ $coupon['distributor'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <img src="{{ $coupon['barcode'] }}" alt="Barcode" style="width:100%; height:40px; margin:5px 0;">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning text-center">
            No coupons available to print.
        </div>
    @endif

</div>

<style>
    @media print {
        .card { page-break-inside: avoid; }
        body { -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
