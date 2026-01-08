@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>
            {{ __('Product') }}
        </h4>
    </div>

    <!-- Flash Deal Alert -->
    @if ($flashSale)
        <div>
            <div class="alert flash-deal-alert d-flex justify-content-between align-items-center">
                <div>
                    <div class="deal-title">{{ __('Flash Sale Coming Soon') }}</div>
                    <span class="deal-text">{{ $flashSale->name }}</span>
                </div>
                <div class="countdown d-flex align-items-center">
                    <!-- Days -->
                    <div class="countdown-section">
                        <div class="countdown-label">Days</div>
                        <div id="days" class="countdown-time">00</div>
                    </div>
                    <!-- Hours -->
                    <div class="countdown-section">
                        <div class="countdown-label">Hours</div>
                        <div id="hours" class="countdown-time">00</div>
                    </div>
                    <!-- Minutes -->
                    <div class="countdown-section">
                        <div class="countdown-label">Minutes</div>
                        <div id="minutes" class="countdown-time">00</div>
                    </div>
                    <!-- Seconds -->
                    <div class="countdown-section">
                        <div class="countdown-label">Seconds</div>
                        <div id="seconds" class="countdown-time">00</div>
                    </div>
                </div>
                @hasPermission('shop.flashSale.show')
                    <a href="{{ route('shop.flashSale.show', $flashSale->id) }}" class="btn btn-primary py-2.5">
                        Add Product
                    </a>
                @endhasPermission
            </div>
        </div>
    @endif
    <!-- End Flash Deal Alert -->

    <div class="container-fluid mt-3">

        <div class="card my-3">
            <div class="card-body">

                <div class="d-flex gap-2 pb-2">
                    <h5>
                        {{ __('Filter Products') }}
                    </h5>
                </div>

                <form action="" method="GET">
                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <x-select label="Category" name="category" placeholder="Select Category">
                                <option value="">
                                    {{ __('Select Category') }}
                                </option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </x-select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <x-select label="Brand" name="brand" placeholder="All Brand">
                                <option value="">
                                    {{ __('All Brand') }}
                                </option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </x-select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <x-select label="Color" name="color" placeholder="All Color">
                                <option value="">
                                    {{ __('All Color') }}
                                </option>
                                @foreach ($colors as $color)
                                    <option value="{{ $color->id }}"
                                        {{ request('color') == $color->id ? 'selected' : '' }}>
                                        {{ $color->name }}
                                    </option>
                                @endforeach
                            </x-select>
                        </div>

                    </div>

                    <div class="mt-2 d-flex gap-2 justify-content-end">
                        <a href="{{ route('shop.product.index') }}" class="btn btn-light py-2 px-4">
                            {{ __('Reset') }}
                        </a>
                        <button type="submit" class="btn btn-primary py-2 px-4">
                            {{ __('Filter Data') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-3 card">
            <div class="card-body">

                <form action="" class="d-flex align-items-center justify-content-between gap-3 mb-3">
                    <div class="input-group" style="max-width: 400px">
                        <input type="text" name="search" class="form-control"
                            placeholder="{{ __('Search by product name') }}" value="{{ request('search') }}">
                        <button type="submit" class="input-group-text btn btn-primary">
                            <i class="fa fa-search"></i> {{ __('Search') }}
                        </button>
                    </div>
                    @hasPermission('shop.product.create')
                        <a href="{{ route('shop.product.create') }}" class="btn py-2 btn-primary">
                            <i class="fa fa-plus-circle"></i>
                            {{ __('Create New') }}
                        </a>
                    @endhasPermission
                </form>

                <div class="table-responsive">
                    <table class="table border table-responsive-lg">
                        <thead>
                            <tr>
                                <!-- Added checkbox column for bulk delete functionality -->
                                <th class="text-center">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th class="text-center">{{ __('SL') }}</th>
                                <th>{{ __('Thumbnail') }}</th>
                                <th>{{ __('Product Name') }}</th>
                                <th class="text-center">{{ __('Price') }}</th>
                                <th class="text-center">{{ __('Discount Price') }}</th>
                                <th class="text-center">
                                    {{ __('Verify Status') }}
                                </th>
                                @hasPermission('shop.product.toggle')
                                    <th class="text-center">{{ __('Status') }}</th>
                                @endhasPermission
                                <th class="text-center">{{ __('Action') }}</th>

                            </tr>
                        </thead>
                        @forelse($products as $key => $product)
                            <tr>
                                <!-- Added checkbox for bulk delete functionality -->
                                <td class="text-center">
                                    <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                                </td>
                                <td class="text-center">{{ ++$key }}</td>

                                <td>
                                    <div class="product-image">
                                        <img src="{{ $product->thumbnail }}">
                                    </div>
                                </td>

                                <td>{{ Str::limit($product->name, 50, '...') }}</td>

                                <td class="text-center">
                                    {{ showCurrency($product->price) }}
                                </td>

                                <td class="text-center">
                                    {{ showCurrency($product->discount_price) }}
                                </td>

                                <td class="text-center" style="min-width: 110px">
                                    @if ($product->is_approve)
                                        <span class="status-approved">
                                            <i class="fa fa-check-circle text-success"></i> {{ __('Approved') }}
                                        </span>
                                    @else
                                        <span class="status-pending" data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="Your product status is pending because admin hasn't approved it. When admin will approve your product, it will be show as approved.">
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                            {{ __('Pending') }}
                                        </span>
                                    @endif
                                </td>

                                @hasPermission('shop.product.toggle')
                                    <td class="text-center">
                                        <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="left"
                                            data-bs-title="{{ __('Update product status') }}">
                                            <a href="{{ route('shop.product.toggle', $product->id) }}">
                                                <input type="checkbox" {{ $product->is_active ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </a>
                                        </label>
                                    </td>
                                @endhasPermission

                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        @hasPermission('shop.product.show')
                                            <a href="{{ route('shop.product.show', $product->id) }}"
                                                class="btn btn-outline-primary circleIcon" data-bs-toggle="tooltip"
                                                data-bs-placement="left" data-bs-title="{{ __('View Product') }}">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                        @endhasPermission
                                        @hasPermission('shop.product.barcode')
                                            <a href="{{ route('shop.product.barcode', $product->id) }}"
                                                class="btn btn-outline-info circleIcon" data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                data-bs-title="{{ __('Generate Barcode for this product') }}">
                                                <i class="bi bi-upc-scan"></i>
                                            </a>
                                        @endhasPermission
                                        @hasPermission('shop.product.edit')
                                            <a href="{{ route('shop.product.edit', $product->id) }}"
                                                class="btn btn-outline-primary circleIcon" data-bs-toggle="tooltip"
                                                data-bs-placement="left" data-bs-title="{{ __('Edit Product') }}">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                        @endhasPermission
                                        @hasPermission('shop.product.destroy')
                                            <!-- Changed to use same delete functionality as admin -->
                                            <button class="btn btn-outline-danger circleIcon" onclick="confirmDeletePermanent({{ $product->id }})" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endhasPermission
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="100%">{{ __('No Data Found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                                        {{-- Bulk delete form --}}
                                        <form action="{{ route('shop.product.bulk.destroy') }}" method="POST" id="bulkDeleteForm" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <form action="" method="POST" class="d-none" id="deleteForm">
    @csrf
    @method('DELETE')
</form>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $products->links() }}
        </div>

    </div>
@endsection
@push('scripts')
    <!-- Removed duplicate confirmApprove script -->
    <!-- <script>
        $(".confirmApprove").on("click", function(e) {
            e.preventDefault();
            const url = $(this).attr("href");
            Swal.fire({
                title: "Are you sure?",
                text: "You want to approve this product",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Approve it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    </script> -->

    <!-- Removed duplicate confirmApprove script -->
    <!-- <script>
        $(".confirmApprove").on("click", function(e) {
            e.preventDefault();
            const url = $(this).attr("href");
            Swal.fire({
                title: "Are you sure?",
                text: "You want to approve this product",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Approve it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    </script> -->

    <!-- Added confirmDeletePermanent function to match admin functionality -->
     <script>
    function confirmDeletePermanent(id) {
        const form = document.getElementById('deleteForm');
        form.action = `{{ route('shop.product.destroy', ':id') }}`.replace(':id', id);
        Swal.fire({
            title: "Are you sure?",
            text: "You want to delete this product! If you confirm, it will be deleted permanently.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Yes, Delete it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }

    <!-- Added bulk delete functionality -->
    // Select / Deselect all
    document.getElementById('selectAll')?.addEventListener('change', function (e) {
        const checked = e.target.checked;
        document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = checked);
    });

    // Add Bulk Delete button dynamically near pagination or filters
    (function addBulkButton() {
        const container = document.querySelector('.container-fluid');
        if (!container) return;
        const btn = document.createElement('button');
        btn.className = 'btn btn-danger mb-3';
        btn.innerText = 'Delete Selected';
        btn.type = 'button';
        btn.style.marginRight = '10px';
        btn.addEventListener('click', function () {
            const ids = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(i => i.value);
            if (ids.length === 0) {
                Swal.fire('No product selected', 'Please select at least one product to delete.', 'info');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete ${ids.length} product(s). This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('bulkDeleteForm');
                    // remove old inputs
                    form.querySelectorAll('input[name="ids[]"]').forEach(n => n.remove());
                    ids.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    form.submit();
                }
            });
        });

        // insert before the card (table)
        const card = container.querySelector('.card');
        if (card && card.parentNode) {
            card.parentNode.insertBefore(btn, card);
        }
    })();
</script>
@endpush
@push('css')
    <style>
        /* Flash Deal Alert Styles */
        .flash-deal-alert {
            background: linear-gradient(90deg, #9b34ff, #617eff);
            color: white;
            border-radius: 8px;
            padding: 8px 15px;
        }

        .deal-title {
            font-size: 20px;
        }

        .deal-text {
            font-size: 16px;
        }

        /* Countdown Timer Styles */
        .countdown {
            display: flex;
            gap: 20px;
            /* Space between sections */
        }

        .countdown-section {
            text-align: center;
        }

        .countdown-label {
            font-size: 14px;
            font-weight: bold;
        }

        .countdown-time {
            width: 46px;
            height: 46px;
            font-size: 20px;
            font-weight: bold;
            margin-top: 5px;
            border: 1px solid var(--theme-color);
            padding: 5px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            background-color: var(--theme-color);
        }
    </style>
@endpush
