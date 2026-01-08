@if($coupons->count())
<table class="table table-striped">
    <thead>
        <tr>
            <th>Code</th>
            <th>Sales Value</th>
            <th>Area</th>
            <th>Expired At</th>
            <th>Used At</th>
            <th>Distributor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($coupons as $coupon)
        <tr>
            <td>{{ $coupon->code }}</td>
            <td>{{ number_format($coupon->sales_value, 2) }}</td>
            <td>{{ $coupon->area_name }}</td>
            <td>{{ $coupon->expired_at }}</td>
            <td>{{ $coupon->used_at ? $coupon->used_at : 'Not used' }}</td>
            <td>{{ $coupon->distributor->name ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p class="text-muted">No coupons in this category.</p>
@endif
