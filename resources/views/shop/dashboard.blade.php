@extends('layouts.app')

@section('content')
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div> {{ __('Dashboard') }}
                    <div class="page-title-subheading">
                        {{ __('This is a shop dashboard') }}.
                    </div>
                </div>
            </div>
        </div>
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

    <div class="row">

        <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
            <div class="dashboard-summery bg-midnight-bloom">
                <h2>{{ $totalProduct }}</h2>
                <h3>{{ __('Total Products') }}</h3>
                <div class="icon">
                    <i class="bi bi-basket"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
            <div class="dashboard-summery bg-alternate">
                <h2>{{ $totalOrder }}</h2>
                <h3>{{ __('Total Orders') }}</h3>
                <div class="icon">
                    <i class="bi bi-cart-check"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
            <div class="dashboard-summery bg-grow-early">
                <h2>{{ $totalCategories }}</h2>
                <h3>{{ __('Total Categories') }}</h3>
                <div class="icon">
                    <i class="bi bi-list-stars"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
            <div class="dashboard-summery bg-midnight">
                <h2>{{ $totalBrand }}</h2>
                <h3>{{ __('Total Brands') }}</h3>
                <div class="icon">
                    <i class="bi bi-award-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!---- Order Analytics -->
    @hasPermission('shop.order.index')
        <div class="card">
            <div class="card-body">
                <div class="cardTitleBox">
                    <h5 class="card-title chartTitle">
                        <i class="bi bi-bar-chart"></i> {{ __('Order Analytics') }}
                    </h5>
                </div>

                @php
                    $icons = [
                        'pending' => 'bi-clock',
                        'confirm' => 'bi-bag-check-fill',
                        'processing' => 'bi-arrow-repeat',
                        'pickup' => 'bi-bicycle',
                        'onTheWay' => 'bi-bicycle',
                        'delivered' => 'bi-patch-check-fill',
                        'cancelled' => 'bi-x-circle',
                    ];
                @endphp

                <div class="d-flex flex-wrap gap-3 orderStatus">
                    @foreach ($orderStatuses as $status)
                        <a href="{{ route('shop.order.index', str_replace(' ', '_', $status->value)) }}"
                            class="d-flex align-items-center gap-3 status flex-grow-1">
                            <div>
                                <i class="bi {{ $icons[Str::camel($status->value)] }}"></i>
                                <span>{{ __($status->value) }}</span>
                            </div>
                            <span class="count">{{ ${Str::camel($status->value)} }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endhasPermission

    @php
        $user = auth()->user();
        $isAdmin = true;
        if (!$user->hasRole('root') && ($user->shop || $user->myShop)) {
            $isAdmin = false;
        }
    @endphp

    <!---- Shop Wallet -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="cardTitleBox">
                <h5 class="card-title chartTitle">
                    <i class="bi bi-wallet2"></i> {{ __('Shop Wallet') }}
                </h5>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="wallet py-4 px-3 h-100">
                        <div class="wallet-icon mt-md-1">
                            <img src="{{ asset('assets/images/wallet.png') }}" alt="" width="100%">
                        </div>
                        <h3 class="balance">{{ showCurrency(auth()->user()->wallet->balance) }}</h3>

                        @if ($isAdmin)
                            <div class="title">{{ __('Available Balance') }}</div>
                        @else
                            @hasPermission('shop.withdraw.store')
                                <div class="title">{{ __('Withdrawable Balance') }}</div>

                                <button class="btn btn-primary py-2 px-4 mt-md-1" data-bs-toggle="modal"
                                    data-bs-target="#withdrawModal">
                                    {{ __('Withdraw') }}
                                </button>
                            @endhasPermission
                        @endif
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="row gy-4">

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div>
                                    <div class="amount">{{ showCurrency($pendingWithdraw) }}</div>
                                    <div class="title">{{ __('Pending Withdraw') }}</div>
                                </div>
                                <div class="icon">
                                    <img src="{{ asset('assets/icons/pendingWithdraw.png') }}" alt="icon" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div>
                                    <div class="amount">{{ showCurrency($alreadyWithdraw) }}</div>
                                    <div class="title">{{ __('Already Withdraw') }}</div>
                                </div>
                                <div class="icon">
                                    <img src="{{ asset('assets/icons/alreadyWithdraw.png') }}" alt="icon" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div>
                                    <div class="amount">{{ showCurrency($deniedWithddraw) }}</div>
                                    <div class="title">{{ __('Rejected Withdraw') }}</div>
                                </div>
                                <div class="icon">
                                    <img src="{{ asset('assets/icons/reject.png') }}" alt="icon" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div>
                                    <div class="amount">{{ showCurrency($totalWithdraw) }}</div>
                                    <div class="title">{{ __('Total Withdraw') }}</div>
                                </div>
                                <div class="icon">
                                    <img src="{{ asset('assets/icons/totalEarn.png') }}" alt="icon" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div>
                                    <div class="amount">{{ showCurrency($totalDeliveryCollected) }}</div>
                                    <div class="title">{{ __('Delivery Charge Collected') }}</div>
                                </div>
                                <div class="icon">
                                    <img src="{{ asset('assets/icons/deliveryCharge.png') }}" alt="icon" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div>
                                    <div class="amount">{{ showCurrency($totalPosSales) }}</div>
                                    <div class="title">{{ __('Total Pos Sales') }}</div>
                                </div>
                                <div class="icon">
                                    <img src="{{ asset('assets/icons/cash.png') }}" alt="icon" />
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Withdraw Modal -->
        <form id="withdrawForm" method="POST">
            @csrf
            <div class="modal fade" id="withdrawModal" tabindex="-1">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5">{{ __('Withdraw Request') }}</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <label class="form-label">
                                    {{ __('Withdraw Amount') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="amount" id="amount" class="form-control"
                                    placeholder="Enter amount"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                    required>

                                <p class="text-danger" id="amount-error"></p>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">
                                    {{ __('Name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Enter name" required>
                                <span class="text-danger" id="name-error"></span>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">
                                    {{ __('Contact Number') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="contact_number" id="contact_number" class="form-control"
                                    placeholder="Enter contact number"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                    required>
                                <span class="text-danger" id="contact_number-error"></span>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">{{ __('Any message') }}</label>
                                <textarea name="message" placeholder="Enter message" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" id="submitBtn" class="btn btn-primary">{{ __('Submit') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>

    <!-- Orders Overview -->
    <div class="row">
        <div class="col-xxl-8 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="cardTitleBox">
                        <h5 class="card-title chartTitle">{{ __('Orders Summary') }}</h5>
                        <p class="lastAll"><i class="bi bi-calendar-date pe-1"></i>
                            {{ __('latest 8th orders') }}
                        </p>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><strong>{{ __('Order ID') }}</strong></th>
                                    <th><strong>{{ __('Qty') }}</strong></th>
                                    <th><strong>{{ __('Date') }}</strong></th>
                                    <th><strong>{{ __('Price') }}</strong></th>
                                    <th><strong>{{ __('Status') }}</strong></th>
                                    <th><strong>{{ __('Action') }}</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latestOrders as $order)
                                    <tr>
                                        <td class="tableId">#{{ $order->prefix . $order->order_code }}</td>
                                        <td class="tableId">
                                            {{ $order->products->count() }}
                                        </td>
                                        <td class="tableId">
                                            {{ $order->created_at->format('d M, Y') }}
                                        </td>
                                        <td class="tableId">
                                            {{ showCurrency($order->payable_amount) }}
                                        </td>
                                        @php
                                            $status = Str::ucfirst(str_replace(' ', '', $order->order_status->value));
                                        @endphp
                                        <td class="tableStatus">
                                            <div class="statusItem">
                                                <div class="circleDot animated{{ $status }}"></div>
                                                <div class="statusText">
                                                    <span class="status{{ $status }}">
                                                        {{ $order->order_status->value }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="tableAction">
                                            <a href="{{ route('shop.order.show', $order->id) }}" data-bs-toggle="tooltip"
                                                data-bs-placement="left" data-bs-title="{{ __('Order details') }}"
                                                class="circleIcon btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <a href="{{ route('shop.download-invoice', $order->id) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="left"
                                                data-bs-title="{{ __('Download Invoice') }}"
                                                class="circleIcon btn-outline-success btn btn-sm">
                                                <i class="bi bi-arrow-down-circle"></i>
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 mt-3">
            <div class="card h-100">
                <div class="card-header py-3">
                    <h5 class="card-title m-0">
                        <i class="bi bi-motherboard fz-14"></i> {{ __('Others Overview') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3 orderStatus">
                        <a href="{{ route('shop.color.index') }}"
                            class="d-flex align-items-center gap-3 status flex-grow-1">
                            <div>
                                <i class="bi bi-palette-fill"></i>
                                <span>{{ __('Total Colors') }}</span>
                            </div>
                            <span class="count">{{ $totalColor }}</span>
                        </a>

                        <a href="{{ route('shop.unit.index') }}"
                            class="d-flex align-items-center gap-3 status flex-grow-1">
                            <div>
                                <i class="bi bi-unity"></i>
                                <span>{{ __('Total Unites') }}</span>
                            </div>
                            <span class="count">{{ $totalUnit }}</span>
                        </a>

                        <a href="{{ route('shop.size.index') }}"
                            class="d-flex align-items-center gap-3 status flex-grow-1">
                            <div>
                                <i class="bi bi-bounding-box-circles"></i>
                                <span>{{ __('Total Sizes') }}</span>
                            </div>
                            <span class="count">{{ $totalSize }}</span>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Top Selling Products -->
        <div class="col-xxl-4 col-lg-6 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="cardTitleBox">
                        <h5 class="card-title chartTitle">
                            <i class="bi bi-bag-check fz-16"></i> {{ __('Top Selling Products') }}
                        </h5>
                    </div>

                    <div class="d-flex flex-column gap-1">
                        @foreach ($topSellingProducts as $product)
                            <a href="{{ route('shop.product.show', $product->id) }}" class="customar-section">
                                <div class="customat-details">
                                    <div class="customar-image">
                                        <img src="{{ $product->thumbnail }}" alt="">
                                    </div>
                                    <div class="customar-about">
                                        <p class="text-dark name">{{ Str::limit($product->name, 30, '...') }}</p>
                                        <p class="order">{{ __('Rating') }}:
                                            {{ number_format($product->reviews->avg('rating'), 1) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="border rounded text-primary px-2 py-1 flex-shrink-0" style="font-size: 13px">
                                    <div>{{ __('Sold') }}: {{ $product->orders_count }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Rating Products -->
        <div class="col-xxl-4 col-lg-6 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="cardTitleBox">
                        <h5 class="card-title chartTitle">
                            <i class="bi bi-stars fz-16"></i> {{ __('Top Rating Products') }}
                        </h5>
                    </div>

                    <div class="d-flex flex-column gap-1">
                        @foreach ($topReviewProducts as $product)
                            <a href="{{ route('shop.product.show', $product->id) }}" class="customar-section">
                                <div class="customat-details">
                                    <div class="customar-image">
                                        <img src="{{ $product->thumbnail }}" alt="">
                                    </div>
                                    <div class="customar-about">
                                        <p class="name text-dark">{{ Str::limit($product->name, 30, '...') }}</p>
                                        <p class="order">{{ __('Sold') }}: {{ $product->orders->count() }}</p>
                                    </div>
                                </div>
                                <div class="border rounded text-primary px-2 py-1 flex-shrink-0" style="font-size: 13px">
                                    <div>{{ __('Rating') }}: <i class="bi bi-star-fill text-warning"></i>
                                        {{ number_format($product->average_rating, 1) }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Most Favorite Products -->
        <div class="col-xxl-4 col-lg-6 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="cardTitleBox">
                        <h5 class="card-title chartTitle">
                            <i class="bi bi-bag-heart fz-16"></i> {{ __('Most Favorite Products') }}
                        </h5>
                    </div>

                    <div class="d-flex flex-column gap-1">
                        @foreach ($topFavorites as $product)
                            <a href="{{ route('shop.product.show', $product->id) }}" class="customar-section">
                                <div class="customat-details">
                                    <div class="customar-image">
                                        <img src="{{ $product->thumbnail }}" alt="">
                                    </div>
                                    <div class="customar-about">
                                        <p class="name text-dark">{{ Str::limit($product->name, 30, '...') }}</p>
                                        <div class="d-flex gap-2 align-items-center">
                                            <p class="order">{{ __('Sold') }}: {{ $product->orders->count() }}</p>
                                            <div class="border-start" style="width: 1px; height: 14px;"></div>
                                            <p class="order">
                                                {{ __('Rating') }}: <i class="bi bi-star-fill text-warning"></i>
                                                {{ number_format($product->average_rating, 1) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="border rounded text-danger px-2 py-1 flex-shrink-0" style="font-size: 16px">
                                    <div>{{ $product->favorites_count }} <i class="bi bi-heart-fill text-danger"></i>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    <script>
        $('#withdrawForm').on('submit', function(e) {
            e.preventDefault();
            const amount = $('#amount').val();
            const name = $('#name').val();
            const contact_number = $('#contact_number').val();
            const message = $('#message').val();
            $('#submitBtn').prop('disabled', true);
            $.ajax({
                url: "{{ route('shop.withdraw.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    amount: amount,
                    name: name,
                    contact_number: contact_number,
                    message: message,
                },
                success: function(response) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "Ok"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                },
                error: function(response) {
                    $('#submitBtn').prop('disabled', false);
                    const errors = response.responseJSON.errors;
                    if (errors && errors.amount) {
                        $('#amount').addClass('is-invalid');
                        $('#amount-error').text(errors.amount[0]);
                    }
                    if (errors && errors.name) {
                        $('#name').addClass('is-invalid');
                        $('#name-error').text(errors.name[0]);
                    }
                    if (errors && errors.contact_number) {
                        $('#contact_number').addClass('is-invalid');
                        $('#contact_number-error').text(errors.contact_number[0]);
                    }

                    if (!errors) {
                        Swal.fire({
                            title: response.responseJSON.message,
                            icon: "warning",
                            confirmButtonColor: "#3085d6",
                            confirmButtonText: "Ok"
                        });
                    }
                }

            });
        });
    </script>

    @if ($flashSale)
        <script>
            // Set the start and end date/time
            var startDateAndTime = "{{ $flashSale->start_date }}T{{ $flashSale->start_time }}";
            var endDateAndTime = "{{ $flashSale->end_date }}T{{ $flashSale->end_time }}";
            let startDate = new Date(startDateAndTime).getTime();
            let endDate = new Date(endDateAndTime).getTime();

            // Update the countdown every 1 second
            let countdownInterval = setInterval(() => {
                let now = new Date().getTime();

                // If current time is before the start date, show "Deal Coming" message
                if (now < startDate) {
                    let distanceToStart = startDate - now;

                    // Time calculations for days, hours, minutes, and seconds
                    let days = Math.floor(distanceToStart / (1000 * 60 * 60 * 24));
                    let hours = Math.floor((distanceToStart % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((distanceToStart % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distanceToStart % (1000 * 60)) / 1000);

                    // Display the countdown with a "Deal Coming" message
                    document.getElementById("days").innerHTML = String(days).padStart(2, '0');
                    document.getElementById("hours").innerHTML = String(hours).padStart(2, '0');
                    document.getElementById("minutes").innerHTML = String(minutes).padStart(2, '0');
                    document.getElementById("seconds").innerHTML = String(seconds).padStart(2, '0');
                    return;
                }

                // Once the current time is after the start date and before the end date, show the active countdown
                let distance = endDate - now;

                // If the deal has ended, stop the countdown and show the message
                if (distance < 0) {
                    clearInterval(countdownInterval);
                    document.getElementById("days").innerHTML = "00";
                    document.getElementById("hours").innerHTML = "00";
                    document.getElementById("minutes").innerHTML = "00";
                    document.getElementById("seconds").innerHTML = "00";
                    document.querySelector(".deal-text").innerHTML = "Deal Ended!";
                    return;
                }

                // Time calculations for days, hours, minutes, and seconds
                let days = Math.floor(distance / (1000 * 60 * 60 * 24));
                let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Display the result
                document.getElementById("days").innerHTML = String(days).padStart(2, '0');
                document.getElementById("hours").innerHTML = String(hours).padStart(2, '0');
                document.getElementById("minutes").innerHTML = String(minutes).padStart(2, '0');
                document.getElementById("seconds").innerHTML = String(seconds).padStart(2, '0');
            }, 1000);
        </script>
    @endif
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
