@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">

        <h4> {{ __('All Coupons') }} </h4>

        <div class="d-flex gap-2">
            @hasPermission('admin.distributor.coupons')
            <a href="{{ route('admin.coupons.create') }}" class="btn py-2 btn-primary">
                <i class="bi bi-patch-plus"></i>
               {{__('Create New')}}
            </a>
            @endhasPermission
        </div>
        
    </div>
    <div class="mt-4">
        <div class="row">
            <div class="col-lg-12 mb-3">
                <div class="card rounded-12">
                    <div class="card-body">
                        <div class="table-responsive">
                            <form method="GET" action="{{ route('admin.coupons.index') }}" class="row g-3 mb-4">
                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="">{{ __('All Status') }}</option>
                                        <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                                        <option value="used" {{ request('status')=='used' ? 'selected' : '' }}>Used</option>
                                        <option value="expired" {{ request('status')=='expired' ? 'selected' : '' }}>Expired</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <select name="distributor_id" class="form-select">
                                        <option value="">{{ __('All Distributors') }}</option>
                                        @foreach($distributors as $distributor)
                                            <option value="{{ $distributor->id }}" {{ request('distributor_id') == $distributor->id ? 'selected' : '' }}>
                                                {{ $distributor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="area_name" id="area_name" class="form-select" style="width: 100%; border-radius: 6px; padding: 6px 10px; font-size: 14px;">
                                        <option value="">{{ __('Select Area') }}</option>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area }}">{{ $area }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-2">
                                    <input type="number" name="points_min" class="form-control" placeholder="{{ __('Min Points') }}" value="{{ request('points_min') }}">
                                </div>

                                <div class="col-md-2">
                                    <input type="number" name="points_max" class="form-control" placeholder="{{ __('Max Points') }}" value="{{ request('points_max') }}">
                                </div>
                                            
                               <div class="col-md-3">
                                  <input type="date" name="created_from" class="form-control" value="{{ request('created_from') }}">
                               </div>
                               <div class="col-md-3">
                                 <input type="date" name="created_to" class="form-control" value="{{ request('created_to') }} ">
                               </div>


                                <div class="col-md-1 d-grid gap-1">
                                    <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                                   
                                </div>
                                <div class="col-md-1 d-grid gap-1">
                                   
                                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">{{ __('Clear') }}</a>
                                </div>
                            </form>
                            <table class="table">
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

                                    @foreach ($coupons as $coupon)
                                        <tr>
                                            <td><input type="checkbox" class="coupon-checkbox" value="{{ $coupon->id }}"></td>
                                            <td>{{ $coupon->code }}</td>
                                            <td>{{ $coupon->distributor->name ?? 'N/A' }}</td>
                                            <td>{{ $coupon->area_name }}</td>
                                            <td>{{ showCurrency ($coupon->sales_value) }}</td>
                                            <td>{{ $coupon->points }}</td>
                                            <td>
                                                {{ Carbon\Carbon::parse($coupon->expired_at)->format('M d, Y h:i a') }}
                                            </td>
                                            <td>
                                              {{ $coupon->used_at ? $coupon->used_at : 'Not used' }}
                                            </td>

                                            <td>
                                                <span class="badge {{ $coupon->status == 'active' ? 'bg-success' : 'bg-danger' }}">
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
                         {{-- ✅ Pagination --}}
                         @if ($coupons->hasPages())
                                <div class="d-flex justify-content-center mt-3">
                                   {{ $coupons->links('pagination::bootstrap-5') }}
                               </div>
                         @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle toggle printed button
    document.querySelectorAll('.toggle-printed-btn').forEach(button => {
        button.addEventListener('click', function() {
            const couponId = this.getAttribute('data-coupon-id');
            const row = this.closest('tr');
            const statusBadge = row.querySelectorAll('.badge')[1]; // Select the is_printed badge (second badge in row)

            // Send AJAX request to toggle printed status
            fetch(`/admin/coupons/${couponId}/toggle-printed`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the badge text and class
                    if (data.is_printed) {
                        statusBadge.textContent = 'Yes';
                        statusBadge.className = 'badge bg-info';
                    } else {
                        statusBadge.textContent = 'No';
                        statusBadge.className = 'badge bg-secondary';
                    }
                } else {
                    alert('Error updating print status. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating print status. Please try again.');
            });
        });
    });
});
</script>

<style>
.pagination {
    margin-bottom: 0;
}
.page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.page-link {
    color: #0d6efd;
}
.page-link:hover {
    background-color: #e9f3ff;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle toggle printed button
    document.querySelectorAll('.toggle-printed-btn').forEach(button => {
        button.addEventListener('click', function() {
            const couponId = this.getAttribute('data-coupon-id');
            const row = this.closest('tr');
            const statusBadge = row.querySelectorAll('.badge')[1];

            fetch(`/admin/coupons/${couponId}/toggle-printed`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_printed) {
                        statusBadge.textContent = 'Yes';
                        statusBadge.className = 'badge bg-info';
                    } else {
                        statusBadge.textContent = 'No';
                        statusBadge.className = 'badge bg-secondary';
                    }
                } else {
                    alert('Error updating print status. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating print status. Please try again.');
            });
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const storageKey = 'selected_coupons_all';
    
    // Load selected coupons from localStorage
    function loadSelectedCoupons() {
        const stored = localStorage.getItem(storageKey);
        return stored ? JSON.parse(stored) : [];
    }
    
    // Save selected coupons to localStorage
    function saveSelectedCoupons(selectedIds) {
        localStorage.setItem(storageKey, JSON.stringify(selectedIds));
    }
    
    // Initialize checkboxes based on stored selections
    function initializeCheckboxes() {
        const selectedIds = loadSelectedCoupons();
        document.querySelectorAll('.coupon-checkbox').forEach(checkbox => {
            checkbox.checked = selectedIds.includes(checkbox.value);
        });
        updateSelectAllState();
    }
    
    // Update select all checkbox state
    function updateSelectAllState() {
        const selectAllCheckbox = document.getElementById('select-all');
        if (selectAllCheckbox) {
            const checkboxes = document.querySelectorAll('.coupon-checkbox');
            const checkedCount = document.querySelectorAll('.coupon-checkbox:checked').length;
            selectAllCheckbox.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
        }
    }
    
    // Handle individual checkbox changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('coupon-checkbox')) {
            let selectedIds = loadSelectedCoupons();
            const couponId = e.target.value;
            
            if (e.target.checked) {
                if (!selectedIds.includes(couponId)) {
                    selectedIds.push(couponId);
                }
            } else {
                selectedIds = selectedIds.filter(id => id !== couponId);
            }
            
            saveSelectedCoupons(selectedIds);
            updateSelectAllState();
        }
    });
    
    // Select all checkbox functionality
    document.addEventListener('change', function(e) {
        if (e.target.id === 'select-all') {
            let selectedIds = loadSelectedCoupons();
            const currentPageCheckboxes = document.querySelectorAll('.coupon-checkbox');
            
            currentPageCheckboxes.forEach(checkbox => {
                checkbox.checked = e.target.checked;
                const couponId = checkbox.value;
                
                if (e.target.checked) {
                    if (!selectedIds.includes(couponId)) {
                        selectedIds.push(couponId);
                    }
                } else {
                    selectedIds = selectedIds.filter(id => id !== couponId);
                }
            });
            
            saveSelectedCoupons(selectedIds);
        }
    });
    
    // Initialize on page load
    initializeCheckboxes();
    
    // Print labels button functionality
    const printLabelsBtn = document.getElementById('print-labels-btn');
    if (printLabelsBtn) {
        printLabelsBtn.addEventListener('click', function() {
            const selectedIds = loadSelectedCoupons();

            let url = `/admin/coupons/labels/all`;
            if (selectedIds.length > 0) {
                url += `?ids=${selectedIds.join(',')}`;
            }

            window.open(url, '_blank');
            
            // Clear selections after printing
            localStorage.removeItem(storageKey);
            document.querySelectorAll('.coupon-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectAllState();
            updateSelectedCount();
        });
    }

    // Export Excel button functionality
    const exportExcelBtn = document.getElementById('export-excel-btn');
    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', function() {
            const selectedIds = loadSelectedCoupons();

            let url = `/admin/coupons/export`;
            if (selectedIds.length > 0) {
                url += `?ids=${selectedIds.join(',')}`;
            }

            window.location.href = url;
        });
    }
    
    // Clear selection button functionality
    const clearSelectionBtn = document.getElementById('clear-selection-btn');
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            localStorage.removeItem(storageKey);
            document.querySelectorAll('.coupon-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectAllState();
            updateSelectedCount();
        });
    }
    
    // Update selected count display
    function updateSelectedCount() {
        const selectedIds = loadSelectedCoupons();
        const countElement = document.getElementById('selected-count');
        if (countElement) {
            countElement.textContent = selectedIds.length;
        }
    }
    
    // Update count on page load and when selections change
    updateSelectedCount();
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('coupon-checkbox') || e.target.id === 'select-all') {
            setTimeout(updateSelectedCount, 10);
        }
    });
});
</script>