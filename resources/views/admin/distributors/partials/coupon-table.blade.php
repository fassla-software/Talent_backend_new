@if($coupons->count())
<table class="table table-striped">
    <thead>
        <tr>
           <th><input type="checkbox" id="select-all"> Select All</th>
            <th>Code</th>
            <th>Sales Value</th>
            <th>Status</th>
            <th>Area</th>
            <th>Expired At</th>
            <th>Used At</th>
            <th>Is Printed</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($coupons as $coupon)
        <tr>
            <td><input type="checkbox" class="coupon-checkbox" value="{{ $coupon->id }}"></td>
            <td>{{ $coupon->code }}</td>
            <td>{{ number_format($coupon->sales_value, 2) }}</td>
            <td>
                <span class="badge
                    @if($coupon->status == 'active') bg-success
                    @elseif($coupon->status == 'used') bg-primary
                    @else bg-danger @endif">
                    {{ ucfirst($coupon->status) }}
                </span>
            </td>
            <td>{{ $coupon->area_name }}</td>
            <td>{{ $coupon->expired_at }}</td>
            <td>{{ $coupon->used_at ? $coupon->used_at : 'Not used' }}</td>
            <td>
                <span class="badge {{ $coupon->is_printed ? 'bg-info' : 'bg-secondary' }}">
                    {{ $coupon->is_printed ? 'Yes' : 'No' }}
                </span>
            </td>
            <td>
                <button type="button" class="btn btn-outline-warning btn-sm toggle-printed-btn"
                    data-bs-toggle="tooltip" data-bs-placement="left" title="change Printed status"
                    data-coupon-id="{{ $coupon->id }}">
                    <i class="bi bi-printer"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if(isset($showPagination) && $showPagination && method_exists($coupons, 'links'))
    <div class="d-flex justify-content-center">
        {{ $coupons->links() }}
    </div>
@endif
@else
<p class="text-muted">No coupons in this category.</p>
@endif

