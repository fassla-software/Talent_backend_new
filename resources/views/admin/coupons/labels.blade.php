<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Coupon Labels</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        /* LABEL FIXED SIZE */
        .label-card {
            width: 6cm;
            height: 3cm;
            border: 1px solid #000;
            padding: 5px;
            margin: 10px auto;
            display: block;      /* one under one */
            box-sizing: border-box;
        }

        .label-card img {
            max-width: 100%;
        }

        .qr {
            width: 2cm;
            height: 2cm;
        }

        .barcode {
            width: 100%;
            height: 0.8cm;
        }

        @media print {
            body { margin: 0; padding: 0; }
            .label-card {
                page-break-inside: avoid;
                margin: 0 auto 5px auto;
                border: none;
            }
        }
    </style>
</head>

<body>
    <script>
        // Pass coupon IDs to JavaScript
        const couponIds = @json(collect($data)->pluck('id')->toArray());
        const distributorId = "{{ request()->route('distributor') ?? 'null' }}";

        let printTriggered = false;

        document.addEventListener('keydown', function(event) {
            // Check for Ctrl+P (or Cmd+P)
            if ((event.ctrlKey || event.metaKey) && event.key === 'p') {
                event.preventDefault();

                if (couponIds.length === 0) {
                    alert('No unprinted coupons available for printing.');
                    return;
                }

                if (!printTriggered) {
                    printTriggered = true;

                    fetch(`/admin/coupons/mark-printed/${distributorId}?ids=${couponIds.join(',')}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.print(); // print after success
                        } else {
                            alert('Error updating print status. Please try again.');
                            printTriggered = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error updating print status. Please try again.');
                        printTriggered = false;
                    });
                }
            }
        });
    </script>

    @if(count($data) > 0)
        @foreach($data as $coupon)

            <div class="label-card">

                <div style="display:flex; height: 2cm;">
                    <div style="flex:1;">
                        <img src="{{ $coupon['qr'] }}" class="qr" alt="QR">
                    </div>

                    <div style="flex:1; text-align:center;">
                        <h6 style="margin:0; font-size:14px; font-weight:bold;">
                            {{ $coupon['code'] }}
                        </h6>
                        <p style="margin:0; font-size:12px;">
                            {{ $coupon['distributor'] }}
                        </p>
                    </div>
                </div>

                <img src="{{ $coupon['barcode'] }}" class="barcode" alt="Barcode">

            </div>

        @endforeach

    @else
        <div class="alert alert-warning text-center">
            No coupons available to print.
        </div>
    @endif

</body>
</html>
