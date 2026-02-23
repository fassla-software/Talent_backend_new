@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5 animate-fade-in">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.traders') }}" class="text-primary text-decoration-none">Traders</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-dark mb-0">
                Trader Profile
                <span class="badge bg-soft-primary text-primary fs-4 ms-2">#ID-{{ $trader->id }}</span>
            </h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.inspectionVisit.index', ['trader_id' => $trader->id]) }}" class="btn btn-primary rounded-pill px-4 shadow-sm transition-hover">
                <i class="fa fa-calendar-check me-2"></i> View Visits
            </a>
            <a href="{{ route('admin.ticket.index', ['client_id' => $trader->user_id]) }}" class="btn btn-info rounded-pill px-4 shadow-sm transition-hover">
                <i class="fa fa-ticket-alt me-2"></i> View Tickets
            </a>
            <a href="{{ route('admin.inspectionRequest.index', ['requestor_id' => $trader->user_id]) }}" class="btn btn-success rounded-pill px-4 shadow-sm transition-hover">
                <i class="fa fa-clipboard-check me-2"></i> View Inspection Requests
            </a>
            <a href="{{ route('admin.traders') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm transition-hover">
                <i class="fa fa-arrow-left me-2"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar: Quick Overview -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4 animate-slide-up">
                <div class="card-header bg-gradient-primary py-5 text-center position-relative">
                    <div class="avatar-wrapper mx-auto mb-3 shadow-lg">
                        @if($trader->image)
                            <img src="https://app.talentindustrial.com/plumber/uploads/{{ $trader->image }}" alt="Trader Image" class="avatar-img">
                        @else
                            <div class="avatar-placeholder">
                                <i class="fa fa-user fa-3x text-white-50"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="text-white fw-bold mb-1">{{ $trader->user->name ?? 'N/A' }}</h3>
                    <p class="text-white-50 mb-0"><i class="fa fa-map-marker-alt me-2"></i>{{ $trader->city ?? 'Location N/A' }}</p>
                </div>
                <div class="card-body p-4">
                    <div class="status-badge-container text-center mb-4">
                        <span class="badge w-100 py-3 fs-6 rounded-pill 
                            @if ($trader->status === 'APPROVED') bg-soft-success text-success
                            @elseif ($trader->status === 'PENDING') bg-soft-warning text-warning
                            @elseif ($trader->status === 'REJECTED') bg-soft-danger text-danger
                            @else bg-soft-secondary text-secondary
                            @endif
                        ">
                            <i class="fa fa-circle me-2"></i> {{ $trader->status ?? 'No Status' }}
                        </span>
                    </div>

                    <div class="info-list">
                        <div class="info-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="text-muted"><i class="fa fa-user-tag me-2"></i> Role</span>
                            <span class="fw-bold text-dark">Trader</span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="text-muted"><i class="fa fa-phone me-2"></i> Phone</span>
                            <span class="fw-bold text-dark">{{ $trader->user->phone ?? '---' }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="text-muted"><i class="fa fa-map me-2"></i> City</span>
                            <span class="fw-bold text-dark">{{ $trader->city ?? '---' }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="text-muted"><i class="fa fa-layer-group me-2"></i> Area</span>
                            <span class="fw-bold text-dark">{{ $trader->area ?? '---' }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="text-muted"><i class="fa fa-check-circle me-2"></i> Verified</span>
                            <span class="fw-bold {{ $trader->is_verified ? 'text-success' : 'text-danger' }}">
                                {{ $trader->is_verified ? 'Verified' : 'Unverified' }}
                            </span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="text-muted"><i class="fa fa-user-tie me-2"></i> Added By (Envoy)</span>
                            <span class="fw-bold text-dark">{{ $trader->inspector->name ?? '---' }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="text-muted"><i class="fa fa-phone-alt me-2"></i> Envoy Phone</span>
                            <span class="fw-bold text-dark">{{ $trader->inspector->phone ?? '---' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Overview Card -->
            <div class="card border-0 shadow-lg rounded-4 animate-slide-up" style="animation-delay: 0.1s;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Performance Stats</h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 rounded-4 bg-soft-primary text-center h-100">
                                <p class="text-muted mb-1 small uppercase fw-bold">Used Coupons</p>
                                <h4 class="fw-bold text-primary mb-0">{{ $totalCoupons }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-4 bg-soft-success text-center h-100">
                                <p class="text-muted mb-1 small uppercase fw-bold">Sales Value</p>
                                <h4 class="fw-bold text-success mb-0">{{ number_format($totalSalesValue, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 rounded-4 bg-soft-warning text-center">
                                <p class="text-muted mb-1 small uppercase fw-bold">Current Level</p>
                                <h4 class="fw-bold text-warning mb-0">{{ $currentLevel ? $currentLevel->level : 'N/A' }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-8">
            <!-- General Information -->
            <div class="card border-0 shadow-lg rounded-4 mb-4 animate-slide-up" style="animation-delay: 0.2s;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">General Information</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border-start border-4 border-primary">
                                <label class="text-muted small fw-bold d-block mb-1">REGISTRATION DATE</label>
                                <span class="fs-5 fw-semibold">{{ $trader->created_at ? $trader->created_at->format('M d, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border-start border-4 border-info">
                                <label class="text-muted small fw-bold d-block mb-1">LAST UPDATED</label>
                                <span class="fs-5 fw-semibold">{{ $trader->updated_at ? $trader->updated_at->diffForHumans() : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border-start border-4 border-warning">
                                <label class="text-muted small fw-bold d-block mb-1">ACCOUNT ID</label>
                                <span class="fs-5 fw-semibold">TRD-{{ str_pad($trader->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border-start border-4 border-success">
                                <label class="text-muted small fw-bold d-block mb-1">NATIONALITY ID</label>
                                <span class="fs-5 fw-semibold">{{ $trader->nationality_id ?? '---' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Documents -->
            <div class="card border-0 shadow-lg rounded-4 mb-4 animate-slide-up" style="animation-delay: 0.3s;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Verification Documents</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="document-card shadow-sm rounded-4 p-3 border">
                                <h6 class="text-center fw-bold text-muted mb-3">Nationality ID Front</h6>
                                <div class="image-preview rounded-3 overflow-hidden">
                                    @if($trader->nationality_image1)
                                        <img src="https://app.talentindustrial.com/plumber/uploads/{{ $trader->nationality_image1 }}" class="img-fluid document-img" onclick="openLightbox(this.src)">
                                    @else
                                        <div class="no-doc-placeholder">
                                            <i class="fa fa-file-image fa-2x"></i>
                                            <p class="mb-0 mt-2">No Front ID</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="document-card shadow-sm rounded-4 p-3 border">
                                <h6 class="text-center fw-bold text-muted mb-3">Nationality ID Back</h6>
                                <div class="image-preview rounded-3 overflow-hidden">
                                    @if($trader->nationality_image2)
                                        <img src="https://app.talentindustrial.com/plumber/uploads/{{ $trader->nationality_image2 }}" class="img-fluid document-img" onclick="openLightbox(this.src)">
                                    @else
                                        <div class="no-doc-placeholder">
                                            <i class="fa fa-file-image fa-2x"></i>
                                            <p class="mb-0 mt-2">No Back ID</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coupons Table Section -->
            @if($totalCoupons)
            <div class="card border-0 shadow-lg rounded-4 animate-slide-up" style="animation-delay: 0.4s;">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Recent Used Coupons</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        @php
                            $usedCouponsList = $trader->usedCoupons->where('status','used');
                        @endphp
                        @include('admin.traders.partials.coupon-table', ['coupons' => $usedCouponsList])
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-body p-0 text-center">
                <img src="" id="lightboxImg" class="img-fluid rounded-4 shadow-lg border border-5 border-white">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    /* Color Palette & Gradients */
    :root {
        --primary-gradient: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        --soft-primary: #e7f1ff;
        --soft-success: #e8f5e9;
        --soft-warning: #fff3e0;
        --soft-danger: #ffebee;
        --soft-info: #e0f7fa;
    }

    .bg-gradient-primary { background: var(--primary-gradient); }
    .bg-soft-primary { background-color: var(--soft-primary); }
    .bg-soft-success { background-color: var(--soft-success); }
    .bg-soft-warning { background-color: var(--soft-warning); }
    .bg-soft-danger { background-color: var(--soft-danger); }
    .bg-soft-info { background-color: var(--soft-info); }

    /* Layout & Cards */
    .card { transition: all 0.3s cubic-bezier(.25,.8,.25,1); border: 1px solid rgba(0,0,0,0.05); }
    .shadow-lg { box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important; }
    
    /* Avatar & Images */
    .avatar-wrapper {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid white;
        background: white;
    }
    .avatar-img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-placeholder { width: 100%; height: 100%; background: #ccc; display: flex; align-items: center; justify-content: center; }
    
    .image-preview {
        height: 200px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed #dee2e6;
    }
    .document-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    .document-img:hover { transform: scale(1.05); }
    .no-doc-placeholder { color: #adb5bd; text-align: center; }

    /* Animations */
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    
    .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }

    .transition-hover:hover { transform: translateY(-2px); }
    
    /* Buttons */
    .rounded-pill { border-radius: 50rem !important; }
    
    .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; line-height: 1; color: #adb5bd; }

    /* Info Items */
    .info-item { border-bottom: 1px solid rgba(0,0,0,0.05); }
    .info-item:last-child { border-bottom: none; }
</style>
@endpush

@push('scripts')
<script>
    function openLightbox(src) {
        const modal = new bootstrap.Modal(document.getElementById('lightboxModal'));
        document.getElementById('lightboxImg').src = src;
        modal.show();
    }
</script>
@endpush
@endsection
