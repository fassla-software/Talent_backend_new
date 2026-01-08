@extends('layouts.app')
@section('content')
    <div>
        <h4>{{ __('Product List') }}</h4>
    </div>

    <form action="" method="GET" class="card card-body">

        @if (request('approve'))
            <input type="hidden" name="approve" value="{{ request('approve') }}">
        @else
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-3">
                <x-select label="Shop" name="shop">
                    <option value="">
                        {{ __('All Shop') }}
                    </option>
                    @foreach ($shops as $shop)
                        <option value="{{ $shop->id }}" {{ request('shop') == $shop->id ? 'selected' : '' }}>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </x-select>
            </div>
        </div>

        <div class="mt-2 d-flex gap-2 justify-content-end">
            <a href="{{ route('admin.product.index') }}" class="btn btn-light py-2 px-4">
                {{ __('Reset') }}
            </a>
            <button type="submit" class="btn btn-primary py-2 px-4">
                {{ __('Filter Data') }}
            </button>
        </div>
    </form>

    <div class="container-fluid mt-3">

        <div class="mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border table-responsive-lg">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th class="text-center">{{ __('SL') }}.</th>
                                <th>{{ __('Thumbnail') }}</th>
                                <th style="min-width: 150px">{{ __('Product Name') }}</th>
                                <th style="min-width: 100px">{{ __('Shop') }}</th>
                                <th class="text-center">{{ __('Price') }}</th>
                                <th class="text-center" style="min-width: 120px">{{ __('Discount Price') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        @forelse($products as $key => $product)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                                </td>
                                <td class="text-center">{{ ++$key }}</td>

                                <td>
                                    <img src="{{ $product->thumbnail }}" width="50">
                                </td>

                                <td>{{ Str::limit($product->name, 50, '...') }}</td>

                                <td>
                                    <a class="text-decoration-none text-dark"
                                        href="{{ route('admin.shop.show', $product->shop_id) }}">
                                        {{ $product->shop->name }}
                                    </a>
                                </td>

                                <td class="text-center">
                                    {{ showCurrency($product->price) }}
                                </td>

                                <td class="text-center">
                                    {{ showCurrency($product->discount_price) }}
                                </td>

                                <td class="text-center">
                                    @if (!$product->is_approve)
                                        <div class="d-flex gap-3 justify-content-center">
                                            @hasPermission('admin.product.approve')
                                            <a href="{{ route('admin.product.approve', $product->id) }}" class="btn btn-success btn-sm confirmApprove">{{ __('Approved') }}</a>
                                            @endhasPermission
                                            @hasPermission('admin.product.destroy')
                                            <button class="btn btn-danger btn-sm"
                                                onclick="confirmDeny({{ $product->id }})">
                                                {{ __('Denied') }}
                                            </button>
                                            @endhasPermission
                                        </div>
                                    @else
                                        <div class="d-flex gap-3 justify-content-center">
                                            @hasPermission('admin.product.show')
                                            <a href="{{ route('admin.product.show', $product->id) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            @endhasPermission
                                            
                                            @hasPermission('admin.product.destroy')
                                            <button class="btn btn-outline-danger btn-sm" onclick="confirmDeletePermanent({{ $product->id }})" title="Delete">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                            @endhasPermission
                                        </div>
                                            
                                            
                                    @endif
                                            
                                            

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="100%">{{ __('No Data Found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $products->withQueryString()->links() }}
        </div>

        <form action="" method="POST" class="d-none" id="deleteForm">
            @csrf
            @method('DELETE')
        </form>

        {{-- Bulk delete form --}}
        <form action="{{ route('admin.product.bulk.destroy') }}" method="POST" id="bulkDeleteForm" class="d-none">
            @csrf
            @method('DELETE')
        </form>

    </div>
        <script>
    function confirmDeleteShopProduct(id) {
        const form = document.getElementById('deleteForm');
        form.action = `{{ route('admin.product.destroy', ':id') }}`.replace(':id', id);

        Swal.fire({
            title: "Are you sure?",
            text: "This product will be permanently deleted and cannot be recovered!",
            icon: "error",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
@endsection
@push('scripts')
    <script>
        // تأكيد الموافقة على المنتج
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

        // تأكيد الحذف العادي
        function confirmDeny(id) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ route('admin.product.destroy', ':id') }}`.replace(':id', id);
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

        // تأكيد الحذف النهائي (للزر الجديد)
//         function confirmDeleteShopProduct(id) {
//             const form = document.getElementById('deleteForm');
//             form.action = `{{ route('admin.product.destroy', ':id') }}`.replace(':id', id);

//             Swal.fire({
//                 title: "Are you sure?",
//                 text: "This product will be permanently deleted and cannot be recovered!",
//                 icon: "error",
//                 showCancelButton: true,
//                 confirmButtonColor: "#d33",
//                 cancelButtonColor: "#6c757d",
//                 confirmButtonText: "Yes, delete it!",
//                 cancelButtonText: "Cancel"
//             }).then((result) => {
//                 if (result.isConfirmed) {
//                     form.submit();
//                 }
//             });
//         }
    </script>
@endpush
@push('scripts')
    <script>
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
