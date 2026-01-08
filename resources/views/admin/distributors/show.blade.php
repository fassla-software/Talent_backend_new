@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Distributor Details</h3>
        <div>
            <a href="{{ route('admin.distributor.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Coupons Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center bg-light p-3 shadow-sm">
                <h6>Total Coupons</h6>
                <p class="fs-4 fw-bold">{{ $totalCoupons }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-success text-white p-3 shadow-sm">
                <h6>Active</h6>
                <p class="fs-4 fw-bold">{{ $activeCoupons }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-primary text-white p-3 shadow-sm">
                <h6>Used</h6>
                <p class="fs-4 fw-bold">{{ $usedCoupons }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-danger text-white p-3 shadow-sm">
                <h6>Expired</h6>
                <p class="fs-4 fw-bold">{{ $expiredCoupons }}</p>
            </div>
        </div>
    </div>

    <!-- Distributor Info Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">ID</h6>
                    <p class="fw-bold">{{ $distributor->id }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Name</h6>
                    <p class="fw-bold">{{ $distributor->name }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Phone</h6>
                    <p class="fw-bold">{{ $distributor->phone }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">City</h6>
                    <p class="fw-bold">{{ $distributor->city }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">State</h6>
                    <p class="fw-bold">{{ $distributor->state }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Notes</h6>
                    <p class="fw-bold">{{ $distributor->notes }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Created At</h6>
                    <p class="fw-bold text-success">{{ $distributor->created_at?->format('Y-m-d H:i') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Updated At</h6>
                    <p class="fw-bold text-info">{{ $distributor->updated_at?->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

            <div class="mb-3">
                <button id="print-labels-btn" class="btn btn-success me-2" data-distributor-id="{{ $distributor->id }}">
                   <i class="bi bi-printer"></i> Print Labels (<span id="selected-count">0</span>)
                </button>
                <button id="export-excel-btn" class="btn btn-primary me-2" data-distributor-id="{{ $distributor->id }}">
                   <i class="bi bi-file-earmark-excel"></i> Export to Excel
                </button>
                <button id="clear-selection-btn" class="btn btn-outline-danger">
                   <i class="bi bi-x-circle"></i> Clear Selection
                </button>
            </div>
    <!-- Coupons Tabs -->
    @if($totalCoupons)
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3" id="couponTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request()->get('tab', 'all') === 'all' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'all', 'page' => 1]) }}" role="tab">All</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request()->get('tab') === 'active' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'active', 'page' => 1]) }}" role="tab">Active</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request()->get('tab') === 'used' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'used', 'page' => 1]) }}" role="tab">Used</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ request()->get('tab') === 'expired' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'expired', 'page' => 1]) }}" role="tab">Expired</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="couponTabsContent">
                @php
                    $currentPage = request()->get('page', 1);
                    $perPage = 15;
                    $currentTab = request()->get('tab', 'all');
                    
                    $allCoupons = $distributor->coupons;
                    $activeCouponsList = $allCoupons->where('status','active');
                    $usedCouponsList = $allCoupons->where('status','used');
                    $expiredCouponsList = $allCoupons->where('status','expired');
                    
                    // Paginate collections
                    $allCouponsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                        $allCoupons->forPage($currentTab === 'all' ? $currentPage : 1, $perPage),
                        $allCoupons->count(),
                        $perPage,
                        $currentTab === 'all' ? $currentPage : 1,
                        ['path' => request()->url(), 'pageName' => 'page']
                    );
                    $allCouponsPaginated->appends(['tab' => 'all']);
                    
                    $activeCouponsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                        $activeCouponsList->forPage($currentTab === 'active' ? $currentPage : 1, $perPage),
                        $activeCouponsList->count(),
                        $perPage,
                        $currentTab === 'active' ? $currentPage : 1,
                        ['path' => request()->url(), 'pageName' => 'page']
                    );
                    $activeCouponsPaginated->appends(['tab' => 'active']);
                    
                    $usedCouponsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                        $usedCouponsList->forPage($currentTab === 'used' ? $currentPage : 1, $perPage),
                        $usedCouponsList->count(),
                        $perPage,
                        $currentTab === 'used' ? $currentPage : 1,
                        ['path' => request()->url(), 'pageName' => 'page']
                    );
                    $usedCouponsPaginated->appends(['tab' => 'used']);
                    
                    $expiredCouponsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                        $expiredCouponsList->forPage($currentTab === 'expired' ? $currentPage : 1, $perPage),
                        $expiredCouponsList->count(),
                        $perPage,
                        $currentTab === 'expired' ? $currentPage : 1,
                        ['path' => request()->url(), 'pageName' => 'page']
                    );
                    $expiredCouponsPaginated->appends(['tab' => 'expired']);
                @endphp

                <!-- All -->
                <div class="tab-pane fade {{ $currentTab === 'all' ? 'show active' : '' }}" id="all" role="tabpanel">
                    @include('admin.distributors.partials.coupon-table', ['coupons' => $allCouponsPaginated, 'showPagination' => $currentTab === 'all'])
                </div>

                <!-- Active -->
                <div class="tab-pane fade {{ $currentTab === 'active' ? 'show active' : '' }}" id="active" role="tabpanel">
                    @include('admin.distributors.partials.coupon-table', ['coupons' => $activeCouponsPaginated, 'showPagination' => $currentTab === 'active'])
                </div>

                <!-- Used -->
                <div class="tab-pane fade {{ $currentTab === 'used' ? 'show active' : '' }}" id="used" role="tabpanel">
                    @include('admin.distributors.partials.coupon-table', ['coupons' => $usedCouponsPaginated, 'showPagination' => $currentTab === 'used'])
                </div>

                <!-- Expired -->
                <div class="tab-pane fade {{ $currentTab === 'expired' ? 'show active' : '' }}" id="expired" role="tabpanel">
                    @include('admin.distributors.partials.coupon-table', ['coupons' => $expiredCouponsPaginated, 'showPagination' => $currentTab === 'expired'])
                </div>
            </div>

        </div>
    </div>
    @endif

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const storageKey = 'selected_coupons_{{ $distributor->id }}';
    
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
            const distributorId = this.getAttribute('data-distributor-id');
            const selectedIds = loadSelectedCoupons();

            let url = `/admin/coupons/labels/${distributorId}`;
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
            const distributorId = this.getAttribute('data-distributor-id');
            const selectedIds = loadSelectedCoupons();
            let url = `/admin/coupons/export/${distributorId}`;
            if (selectedIds.length > 0) {
                url += `?ids=${selectedIds.join(',')}`;
            }

            window.location.href = url;
        
            // Mark exported coupons as printed
            if (selectedIds.length > 0) {
                fetch(`/admin/coupons/mark-exported/${distributorId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ids: selectedIds })
                });
            }
            // Mark exported coupons as printed and clear selections
            setTimeout(() => {
                localStorage.removeItem(storageKey);
                document.querySelectorAll('.coupon-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateSelectAllState();
                updateSelectedCount();
            }, 1000);
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
