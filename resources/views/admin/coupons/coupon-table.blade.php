@if($coupons->count())
<table class="table table-striped">
    <thead>
        <tr>
            <th><input type="checkbox" id="select-all"> Select All</th>
            <th>{{ __('Code') }}</th>
            <th>{{ __('Distributor') }}</th>
            <th>{{ __('Area Name') }}</th>
            <th>{{ __('Base Amount') }}</th>
            <th>{{ __('Value') }}</th>
            <th>{{ __('Expired At') }}</th>
            <th>{{ __('Used At') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Is Printed') }}</th>
            <th>{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($coupons as $coupon)
        <tr>
            <td><input type="checkbox" class="coupon-checkbox" value="{{ $coupon->id }}"></td>
            <td>{{ $coupon->code }}</td>
            <td>{{ $coupon->distributor->name ?? 'N/A' }}</td>
            <td>{{ $coupon->area_name }}</td>
            <td>{{ showCurrency($coupon->sales_value) }}</td>
            <td>{{ $coupon->points }}</td>
            <td>{{ Carbon\Carbon::parse($coupon->expired_at)->format('M d, Y h:i a') }}</td>
            <td>{{ $coupon->used_at ? $coupon->used_at : 'Not used' }}</td>
            <td>
                <span class="badge {{ $coupon->status == 'active' ? 'bg-success' : ($coupon->status == 'used' ? 'bg-primary' : 'bg-danger') }}">
                    {{ ucfirst($coupon->status) }}
                </span>
            </td>
            <td>
                <span class="badge {{ $coupon->is_printed ? 'bg-info' : 'bg-secondary' }}">
                    {{ $coupon->is_printed ? 'Yes' : 'No' }}
                </span>
            </td>
            <td>
                @hasPermission('admin.distributor.coupons')
                <button type="button" class="btn btn-outline-warning circleIcon toggle-printed-btn"
                    data-bs-toggle="tooltip" data-bs-placement="left" title="{{ __('Toggle Printed') }}"
                    data-coupon-id="{{ $coupon->id }}">
                    <i class="bi bi-printer"></i>
                </button>
                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger circleIcon"
                        data-bs-toggle="tooltip" data-bs-placement="left" title="{{ __('Delete') }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
                @endhasPermission
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if(isset($showPagination) && $showPagination && method_exists($coupons, 'links'))
    <div class="d-flex justify-content-center">
        {{ $coupons->links('pagination::bootstrap-5') }}
    </div>
@endif
@else
<p class="text-muted">No coupons in this category.</p>
@endif