@if(!session('dashboard_type'))
<script>
    window.location.href = "{{ route('admin.choose-dashboard') }}";
</script>
@endif








@if(session('dashboard_type') === 'plumber')

@if(auth()->user()->hasRole('root') || auth()->user()->hasRole('admin'))
<!--- Analytics Dashboard --->
<li>
    <a class="menu {{ request()->is('admin/analysis') ? 'active' : '' }}"
        href="{{ url('admin/analysis') }}">
        <span>
            <i class="fa-solid fa-house menu-icon"></i>
            {{ __('Dashboard') }}
        </span>
    </a>
</li>
@endif


<li class="menu-divider">
    <span class="menu-title">{{ __('Plumbers') }}</span>
</li>

@hasPermission('plumber.user.view')
<li>
    <a class="menu {{ $request->routeIs('admin.plumberUsers') ? 'active' : '' }}"
        href="{{ route('admin.plumberUsers') }}">
        <span>
            <i class="fa-solid fa-users menu-icon"></i> <!-- Icon relevant for users -->
            {{ __('Plumber Users') }}
            <span id="plumber-indicator" class="plumber-count" style="display: none;"></span>
        </span>
    </a>
</li>
@endhasPermission

<!-- JavaScript to Fetch Pending Plumber Users Count -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch("{{ route('api.plumberUsers.pending-count') }}") // Define this API route in Laravel
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    const indicator = document.getElementById("plumber-indicator");
                    indicator.textContent = data.count; // Display count
                    indicator.style.display = "inline-block";
                }
            })
            .catch(error => console.error("Error fetching pending plumber users count:", error));
    });
</script>

<!-- CSS for the count indicator -->
<style>
    .plumber-count {
        display: inline-block;
        min-width: 18px;
        height: 18px;
        background-color: red;
        color: white;
        font-size: 12px;
        font-weight: bold;
        text-align: center;
        line-height: 18px;
        border-radius: 50%;
        margin-left: 5px;
        padding: 0 6px;
    }
</style>

@hasPermission('plumber.request.view')

<li>
    <a class="menu {{ $request->routeIs('admin.inspectionRequest.index') ? 'active' : '' }}"
        href="{{ route('admin.inspectionRequest.index') }}">
        <span>
            <i class="fa-solid fa-clipboard-list menu-icon"></i> <!-- Icon for Requests -->
            {{ __('Requests') }}
            <span id="request-indicator" class="request-count" style="display: none;"></span> <!-- Initially hidden -->
        </span>
    </a>
</li>
@endhasPermission

<!-- JavaScript to Fetch Data from the API -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch("https://app.talentindustrial.com/plumber/request") // API URL
            .then(response => response.json())
            .then(data => {
                if (data.requests) { // Ensure 'requests' exists in the response
                    const pendingCount = data.requests.filter(request => request.status === "SEND").length;
                    if (pendingCount > 0) {
                        const indicator = document.getElementById("request-indicator");
                        indicator.textContent = pendingCount; // Display count
                        indicator.style.display = "inline-block";
                    }
                }
            })
            .catch(error => console.error("Error fetching inspection requests:", error));
    });
</script>

<!-- CSS for the red dot with count -->
<style>
    .request-count {
        display: inline-block;
        min-width: 18px;
        height: 18px;
        background-color: red;
        color: white;
        font-size: 12px;
        font-weight: bold;
        text-align: center;
        line-height: 18px;
        border-radius: 50%;
        margin-left: 5px;
        padding: 0 6px;
    }
</style>


@hasPermission('plumber.withdrawal.view')

<li>
    <a class="menu {{ $request->routeIs('withdraw.index') ? 'active' : '' }}"
        href="{{ route('withdraw.index') }}">
        <span>
            <i class="fa-solid fa-wallet menu-icon"></i> <!-- Wallet icon for withdraw -->
            {{ __('Withdraw') }}
            <span id="withdraw-indicator" class="withdraw-count" style="display: none;"></span>
        </span>
    </a>
</li>

@endhasPermission


<li class="menu-divider">
    <span class="menu-title">{{ __('Traders') }}</span>
</li>

@hasPermission('plumber.user.view')
<li>
    <a class="menu {{ $request->routeIs('admin.traders') ? 'active' : '' }}"
        href="{{ route('admin.traders') }}">
        <span>
            <i class="fa-solid fa-handshake menu-icon"></i>
            {{ __('Traders List') }}
        </span>
    </a>
</li>
@endhasPermission

<li class="menu-divider">
    <span class="menu-title">{{ __('Envoys') }}</span>
</li>

@hasPermission('plumber.user.view')
<li>
    <a class="menu {{ $request->routeIs('admin.envoy.index') ? 'active' : '' }}"
        href="{{ route('admin.envoy.index') }}">
        <span>
            <i class="fa-solid fa-user-tie menu-icon"></i>
            {{ __('Envoys List') }}
        </span>
    </a>
</li>
@endhasPermission        
<li class="menu-divider">
    <span class="menu-title">{{ __('Distributor') }}</span>
</li>

@hasPermission('admin.distributor.index')
<li>
    <a class="menu {{ $request->routeIs('admin.distributor.*') ? 'active' : '' }}" href="{{ route('admin.distributor.index') }}">
        <span>
            <i class="fa-solid fa-handshake menu-icon"></i>
            {{ __('Distributors') }}
        </span>
    </a>
            <a class="menu {{ $request->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">
        <span>
            <i class="fa-solid fa-handshake menu-icon"></i>
            {{ __('Coupons') }}
        </span>
    </a>
</li>
@endhasPermission      
        
        
<li class="menu-divider">
    <span class="menu-title">{{ __('Levels') }}</span>
</li>

@hasPermission('level.index')
<li>
    <a class="menu {{ $request->routeIs('level.*') ? 'active' : '' }}" href="{{ route('level.index') }}">
        <span>
            <i class="fa-solid fa-handshake menu-icon"></i>
            {{ __('Levels') }}
        </span>
    </a>
</li>
@endhasPermission
        
        
@hasPermission('plumber.gift.view')

<li class="menu-divider">
    <span class="menu-title">{{ __('Gifts') }}</span>
</li>

@endhasPermission
@hasPermission('plumber.gift.create')

<li>
    <a class="menu {{ $request->routeIs('gift.create') ? 'active' : '' }}"
        href="{{ route('gift.create') }}">
        <span>
            <i class="fa-solid fa-gift menu-icon"></i> <!-- Changed icon to gift -->
            {{ __('Add Gift') }}
        </span>
    </a>
</li>

@endhasPermission

@hasPermission('plumber.gift.view')

<li>
    <a class="menu {{ $request->routeIs('gift.list') ? 'active' : '' }}"
        href="{{ route('gift.list') }}">
        <span>
            <i class="fa-solid fa-list menu-icon"></i> <!-- Changed icon to list -->
            {{ __('Gift List') }} <!-- Updated the link text to "Gift List" -->
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('plumber.gift.view')

<li>
    <a class="menu {{ $request->routeIs('received.gift') ? 'active' : '' }}"
        href="https://app.talentindustrial.com/received-gift">
        <span>
            <i class="fa-solid fa-box-open menu-icon"></i>
            {{ __('Received Gift') }}
            <span id="gift-indicator" class="gift-count" style="display: none;"></span>
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('plumber.gift.create')

<li>
    <a class="menu {{ request()->is('points') ? 'active' : '' }}"
        href="/points">
        <span>
            <i class="fa-solid fa-coins menu-icon"></i>
            {{ __('Points') }}
        </span>
    </a>

</li>

@endhasPermission

<!-- JavaScript to Fetch Pending Gifts from Laravel API -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch("{{ route('api.received-gift.pending-count') }}") // Define this API route in Laravel
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    const indicator = document.getElementById("gift-indicator");
                    indicator.textContent = data.count; // Display count
                    indicator.style.display = "inline-block";
                }
            })
            .catch(error => console.error("Error fetching pending gifts:", error));
    });
</script>

<!-- CSS for the count indicator -->
<style>
    .gift-count {
        display: inline-block;
        min-width: 18px;
        height: 18px;
        background-color: red;
        color: white;
        font-size: 12px;
        font-weight: bold;
        text-align: center;
        line-height: 18px;
        border-radius: 50%;
        margin-left: 5px;
        padding: 0 6px;
    }
</style>




<!-- JavaScript to Fetch Deletion Request Count -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch("{{ route('api.deletion-requests.count') }}") // API route in Laravel
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    const indicator = document.getElementById("deletion-indicator");
                    indicator.textContent = data.count; // Display count
                    indicator.style.display = "inline-block";
                }
            })
            .catch(error => console.error("Error fetching deletion request count:", error));
    });
</script>

<!-- CSS for the count indicator -->
<style>
    .deletion-count {
        display: inline-block;
        min-width: 18px;
        height: 18px;
        background-color: red;
        color: white;
        font-size: 12px;
        font-weight: bold;
        text-align: center;
        line-height: 18px;
        border-radius: 50%;
        margin-left: 5px;
        padding: 0 6px;
    }
</style>




@hasPermission('plumber.category.view')
<li class="menu-divider">
    <span class="menu-title">{{ __('Manage Categories') }}</span>
</li>
<li>
    <a class="menu {{ $request->routeIs('categories.index') ? 'active' : '' }}"
        href="{{ route('categories.index') }}">
        <span>
            <i class="fa-solid fa-folder menu-icon"></i>
            {{ __('Categories') }}
        </span>
    </a>
</li>
<li>
    <a class="menu" href="https://app.talentindustrial.com/plumber-categories/upload">
        <span>
            <i class="fa-solid fa-file-upload menu-icon"></i> <!-- Icon for Upload Cat. 2 Categories -->
            {{ __('Upload Categories') }}
        </span>
    </a>
</li>
@endhasPermission













<!-- JavaScript to Fetch Pending Withdraw Request Count -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch("{{ route('api.withdraw.pending-count') }}") // API route in Laravel
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    const indicator = document.getElementById("withdraw-indicator");
                    indicator.textContent = data.count; // Display count
                    indicator.style.display = "inline-block";
                }
            })
            .catch(error => console.error("Error fetching pending withdraw count:", error));
    });
</script>

<!-- CSS for the count indicator -->
<style>
    .withdraw-count {
        display: inline-block;
        min-width: 18px;
        height: 18px;
        background-color: red;
        color: white;
        font-size: 12px;
        font-weight: bold;
        text-align: center;
        line-height: 18px;
        border-radius: 50%;
        margin-left: 5px;
        padding: 0 6px;
    }
</style>

@hasPermission('plumber.product.view')
<li class="menu-divider">
    <span class="menu-title">{{ __('Manage Products') }}</span>
</li>
<li>
    <a class="menu {{ $request->is('categories/flagged') ? 'active' : '' }}"
        href="{{ url('categories/flagged') }}">
        <span>
            <i class="fa-solid fa-box menu-icon"></i> <!-- Updated icon to box for products -->
            {{ __('Products') }} <!-- Link text for adding a product -->
        </span>
    </a>
</li>
<li>
    <a class="menu" href="https://app.talentindustrial.com/unique-product-form">
        <span>
            <i class="fa-solid fa-plus menu-icon"></i> <!-- Updated icon for Add Product -->
            {{ __('Add Product') }}
        </span>
    </a>
</li>



<li>
    <a class="menu" href="https://app.talentindustrial.com/product-categories/upload">
        <span>
            <i class="fa-solid fa-file-upload menu-icon"></i> <!-- Icon for Upload Product Categories -->
            {{ __('Upload Products') }}
        </span>
    </a>
</li>
@endhasPermission



<li class="menu-divider">
    <span class="menu-title">{{ __('Reports') }}</span>
</li>

<li>
    <a class="menu {{ $request->routeIs('admin.reportDropdown.*') ? 'active' : '' }}"
        href="{{ route('admin.reportDropdown.index') }}">
        <span>
            <i class="fa-solid fa-list menu-icon"></i>
            {{ __('Dropdown Options') }}
        </span>
    </a>
</li>

<li>
    <a class="menu {{ $request->routeIs('admin.inspectionVisit.*') ? 'active' : '' }}"
        href="{{ route('admin.inspectionVisit.index') }}">
        <span>
            <i class="fa-solid fa-clipboard-check menu-icon"></i>
            {{ __('Sales and Marketing Visits') }}
        </span>
    </a>
</li>

<li>
    <a class="menu {{ $request->routeIs('admin.ticket.*') ? 'active' : '' }}"
        href="{{ route('admin.ticket.index') }}">
        <span>
            <i class="fa-solid fa-ticket menu-icon"></i>
            {{ __('Tickets') }}
        </span>
    </a>
</li>

<li class="menu-divider">
    <span class="menu-title">{{ __('Awards') }}</span>
</li>

<li>
    <a class="menu {{ $request->routeIs('admin.award.index') ? 'active' : '' }}"
        href="{{ route('admin.award.index') }}">
        <span>
            <i class="fa-solid fa-trophy menu-icon"></i>
            {{ __('Awards List') }}
        </span>
    </a>
</li>

<li>
    <a class="menu {{ $request->routeIs('admin.award.envoy-awards') || $request->routeIs('admin.award.assign.form') ? 'active' : '' }}"
        href="{{ route('admin.award.envoy-awards') }}">
        <span>
            <i class="fa-solid fa-medal menu-icon"></i>
            {{ __('Envoy Awards') }}
        </span>
    </a>
</li>
        
<li class="menu-divider">
    <span class="menu-title">{{ __('Other') }}</span>
</li>

@hasPermission('plumber.notification.view')

<li>
    <a class="menu {{ $request->routeIs('admin.send-notification') ? 'active' : '' }}"
        href="https://app.talentindustrial.com/admin/send-notification">
        <span>
            <i class="fa-solid fa-bell menu-icon"></i> <!-- Bell icon for notifications -->
            {{ __('Send Notification') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('plumber.delete.view')
<li>
    <a class="menu {{ $request->routeIs('admin.deletion-requests') ? 'active' : '' }}"
        href="https://app.talentindustrial.com/admin/deletion-requests">
        <span>
            <i class="fa-solid fa-trash menu-icon"></i> <!-- Trash icon for deletion -->
            {{ __('Deletion Requests') }}
            <span id="deletion-indicator" class="deletion-count" style="display: none;"></span>
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('plumber.contact.view')
<li>
    <a class="menu {{ request()->routeIs('contact.messages') ? 'active' : '' }}"
        href="{{ route('contact.messages') }}">
        <span>
            <i class="fa-solid fa-envelope menu-icon"></i> <!-- Envelope icon for messages -->
            {{ __('Contact Messages') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('plumber.segment.view')

<li>
    <a class="menu {{ $request->routeIs('segments.index') ? 'active' : '' }}"
        href="{{ route('segments.index') }}">
        <span>
            <i class="fa-solid fa-cogs menu-icon"></i> <!-- Icon changed to a relevant one for segments -->
            {{ __('Segments') }} <!-- Updated link text to "Segments" -->
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('plumber.certificate.view')

<li>
    <a class="menu {{ request()->is('certificates') ? 'active' : '' }}"
        href="https://app.talentindustrial.com/certificates">
        <span>
            <i class="fa-solid fa-certificate menu-icon"></i> <!-- Icon for certificate -->
            {{ __('Certificates') }} <!-- Link text for the certificates page -->
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('plumber.gallery.view')
<li>
    <a class="menu {{ $request->routeIs('dashboard.gallery') ? 'active' : '' }}"
        href="https://app.talentindustrial.com/dashboard/gallery">
        <span>
            <i class="fa-solid fa-images menu-icon"></i>
            {{ __('Gallery') }}
        </span>
    </a>
</li>

@endhasPermission

@endif





@if(session('dashboard_type') === 'ecommerce')

@if(auth()->user()->hasRole('root') || auth()->user()->hasRole('admin'))
<!--- Dashboard --->
<li>
    <a class="menu {{ $request->routeIs('admin.dashboard.*') ? 'active' : '' }}"
        href="{{ route('admin.dashboard.index') }}">
        <span>
            <i class="fa-solid fa-house menu-icon"></i>
            {{ __('Dashboard') }}
        </span>
    </a>
</li>
@endif

@hasPermission(['admin.banner.index', 'admin.ad.index', 'admin.coupon.index', 'admin.customerNotification.index'])
<li class="menu-divider">
    <span class="menu-title">{{ __('Marketing Promotions') }}</span>
</li>
@endhasPermission
@hasPermission('admin.banner.index')
<!--- banner--->
<li>
    <a class="menu {{ $request->routeIs('admin.banner.*') ? 'active' : '' }}" href="{{ route('admin.banner.index') }}">
        <span>
            <i class="fa-solid fa-image menu-icon"></i>
            {{ __('Promotional Banner') }}
        </span>
    </a>
</li>
@endhasPermission



@hasPermission('admin.ad.index')
<!--- ads--->
<li>
    <a class="menu {{ $request->routeIs('admin.ad.*') ? 'active' : '' }}" href="{{ route('admin.ad.index') }}">
        <span>
            <i class="fa-solid fa-photo-film menu-icon"></i>
            {{ __('Ads') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('admin.coupon.index')
<!--- Coupon discount--->
<li>
    <a class="menu {{ $request->routeIs('admin.coupon.*') ? 'active' : '' }}"
        href="{{ route('admin.coupon.index') }}">
        <span>
            <i class="fa-solid fa-ticket menu-icon"></i>
            {{ __('Promo Code') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('admin.customerNotification.index')
<!--- notification--->
<li>
    <a class="menu {{ $request->routeIs('admin.customerNotification.*') ? 'active' : '' }}"
        href="{{ route('admin.customerNotification.index') }}">
        <span>
            <i class="fa-solid fa-bell menu-icon"></i>
            {{ __('Send Notifications') }}
        </span>
    </a>
</li>
@endhasPermission

@endif

@php
use App\Enums\OrderStatus;
$orderStatuses = OrderStatus::cases();
@endphp

@if(session('dashboard_type') === 'ecommerce')

@hasPermission('admin.order.index')
<li class="menu-divider">
    <span class="menu-title">{{ __('Order Handling') }}</span>
</li>

<!--- Orders --->
<li>
    <a class="menu {{ request()->routeIs('admin.order.*') ? 'active' : '' }}" data-bs-toggle="collapse"
        href="#ordersMenu">
        <span>
            <i class="fa-solid fa-cart-shopping menu-icon"></i>
            {{ __('All Orders') }}
        </span>
        <img src="{{ asset('assets/icons/arrowDown.svg') }}" alt="" class="downIcon">
    </a>
    <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.order.*') ? 'show' : '' }}" id="ordersMenu">
        <div class="listBar">
            <a href="{{ route('admin.order.index') }}"
                class="subMenu hasCount {{ request()->url() === route('admin.order.index') ? 'active' : '' }}">
                {{ __('All') }} <span class="count statusAll">{{ $allOrders > 99 ? '99+' : $allOrders }}</span>
            </a>
            @foreach ($orderStatuses as $status)
            <a href="{{ route('admin.order.index', str_replace(' ', '_', $status->value)) }}"
                class="subMenu hasCount {{ request()->url() === route('admin.order.index', str_replace(' ', '_', $status->value)) ? 'active' : '' }}">
                <span>{{ __($status->value) }}</span>
                <span
                    class="count status{{ Str::camel($status->value) }}">{{ ${Str::camel($status->value)} > 99 ? '99+' : ${Str::camel($status->value)} }}</span>
            </a>
            @endforeach
        </div>
    </div>
</li>
@endhasPermission

@hasPermission(['admin.brand.index', 'admin.color.index', 'admin.size.index', 'admin.unit.index'])
<li class="menu-divider">
    <span class="menu-title">{{ __('Product Variants') }}</span>
</li>
@endhasPermission

@hasPermission('admin.brand.index')
<!--- brand --->
<li>
    <a class="menu {{ $request->routeIs('admin.brand.*') ? 'active' : '' }}" href="{{ route('admin.brand.index') }}">
        <span>
            <i class="fa-solid fa-star menu-icon"></i>
            {{ __('Brand') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('admin.color.index')
<!--- color--->
<li>
    <a class="menu {{ $request->routeIs('admin.color.*') ? 'active' : '' }}" href="{{ route('admin.color.index') }}">
        <span>
            <i class="fa-solid fa-palette menu-icon"></i>
            {{ __('Color') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('admin.size.index')
<!--- size--->
<li>
    <a class="menu {{ $request->routeIs('admin.size.*') ? 'active' : '' }}" href="{{ route('admin.size.index') }}">
        <span>
            <i class="fa-solid fa-list-ol menu-icon"></i>
            {{ __('Sizes') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('admin.unit.index')
<!--- unit--->
<li>
    <a class="menu {{ $request->routeIs('admin.unit.*') ? 'active' : '' }}" href="{{ route('admin.unit.index') }}">
        <span>
            <i class="fa-brands fa-unity menu-icon"></i>
            {{ __('Unit') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission(['admin.category.index', 'admin.subcategory.index', 'admin.product.index', 'admin.gift.index'])
<li class="menu-divider">
    <span class="menu-title">{{ __('Product Management') }}</span>
</li>
@endhasPermission

@hasPermission(['admin.category.index', 'admin.subcategory.index'])
<!--- categories--->
<li>
    <a class="menu {{ request()->routeIs('admin.category.*', 'admin.subcategory.*') ? 'active' : '' }}"
        data-bs-toggle="collapse" href="#categoryMenu">
        <span>
            <i class="fa-solid fa-border-all menu-icon"></i>
            {{ __('Categories') }}
        </span>
        <img src="{{ asset('assets/icons/arrowDown.svg') }}" alt="" class="downIcon">
    </a>
    <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.category.*', 'admin.subcategory.*') ? 'show' : '' }}"
        id="categoryMenu">
        <div class="listBar">
            @hasPermission('admin.category.index')
            <a href="{{ route('admin.category.index') }}"
                class="subMenu hasCount {{ request()->routeIs('admin.category.*') ? 'active' : '' }}">
                {{ __('Category') }}
            </a>
            @endhasPermission
            @hasPermission('admin.subcategory.index')
            <!--- sub categories--->
            <a href="{{ route('admin.subcategory.index') }}"
                class="subMenu hasCount {{ request()->routeIs('admin.subcategory.*') ? 'active' : '' }}">
                {{ __('Sub Category') }}
            </a>
            @endhasPermission
        </div>
    </div>
</li>
@endhasPermission

@hasPermission('shop.product.index')
<!--- Products--->
<li>
    <a class="menu {{ $request->routeIs('shop.product.*') ? 'active' : '' }}"
        href="{{ route('shop.product.index') }}">
        <span>
            <i class="fa-brands fa-codepen menu-icon"></i>
            {{ __('Products') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission(['shop.pos.index', 'shop.pos.draft', 'shop.pos.sales'])
<li class="menu-divider">
    <span class="menu-title">{{ __('POS Management') }}</span>
</li>
@endhasPermission

@hasPermission('shop.pos.index')
<!--- POS--->
<li>
    <a class="menu {{ $request->routeIs('shop.pos.index') ? 'active' : '' }}" href="{{ route('shop.pos.index') }}">
        <span>
            <i class="fa-solid fa-store menu-icon"></i>
            {{ __('POS') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('shop.pos.draft')
<!--- Draft --->
<li>
    <a class="menu {{ $request->routeIs('shop.pos.draft') ? 'active' : '' }}" href="{{ route('shop.pos.draft') }}">
        <span>
            <i class="fa-brands fa-firstdraft menu-icon"></i>
            {{ __('Draft') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('shop.pos.sales')
<!--- POS Sales--->
<li>
    <a class="menu {{ $request->routeIs('shop.pos.sales') ? 'active' : '' }}" href="{{ route('shop.pos.sales') }}">
        <span>
            <i class="fa-solid fa-cart-shopping menu-icon"></i>
            {{ __('POS Sales') }}
        </span>
    </a>
</li>
@endhasPermission

@if ($businessModel == 'multi')
@hasPermission(['admin.shop.index', 'admin.product.index'])
<li class="menu-divider">
    <span class="menu-title">{{ __('Manage Shop') }}</span>
</li>
@endhasPermission

@hasPermission('admin.shop.index')
<!--- shops --->
<li>
    <a href="{{ route('admin.shop.index') }}"
        class="menu {{ request()->routeIs('admin.shop.*') ? 'active' : '' }}">
        <span>
            <i class="fa-solid fa-shop menu-icon"></i>
            {{ __('All Shops') }}
        </span>
    </a>
</li>
@endhasPermission

<!--- admin Shop products --->
@hasPermission(['admin.product.index'])
<li>
    <a class="menu {{ request()->routeIs('admin.product.index') ? 'active' : '' }}" data-bs-toggle="collapse"
        href="#shopProducts">
        <span>
            <i class="fa-solid fa-basket-shopping menu-icon"></i>
            {{ __('Shop Products') }}
        </span>
        <img src="{{ asset('assets/icons/arrowDown.svg') }}" alt="" class="downIcon">
    </a>
    <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.product.index') ? 'show' : '' }}"
        id="shopProducts">
        <div class="listBar">
            @if ($generaleSetting?->new_product_approval)
            <a href="{{ route('admin.product.index', 'status=0') }}"
                class="subMenu {{ request()->filled('status') && request()->status == 0 ? 'active' : '' }}"
                title="{{ __('Item Request') }}">
                {{ __('Item Request') }}
            </a>
            @endif

            @if ($generaleSetting?->update_product_approval)
            <a href="{{ route('admin.product.index', 'status=1') }}"
                class="subMenu {{ request()->filled('status') && request()->status == 1 ? 'active' : '' }}"
                title="{{ __('Update Request') }}">
                {{ __('Update Request') }}
            </a>
            @endif

            <a href="{{ route('admin.product.index', 'approve=true') }}"
                class="subMenu {{ request()->filled('approve') && request()->approve == 'true' ? 'active' : '' }}"
                title="{{ __('Accepted Item') }}">
                {{ __('Accepted Item') }}
            </a>
        </div>
    </div>
</li>
@endhasPermission

@hasPermission('admin.flashSale.index')
<li>
    <a href="{{ route('admin.flashSale.index') }}"
        class="menu {{ request()->routeIs('admin.flashSale.*') ? 'active' : '' }}">
        <span>
            <i class="fa-solid fa-bolt menu-icon"></i>
            {{ __('Flash Sales') }}
        </span>
    </a>
</li>
@endhasPermission
@endif

@hasPermission(['admin.rider.index', 'admin.customer.index', 'admin.employee.index', 'admin.role.index'])
<li class="menu-divider">
    <span class="menu-title">{{ __('User Supervision') }}</span>
</li>
@endhasPermission

@hasPermission(['admin.rider.index'])
<!--- rider --->
<li>
    <a class="menu {{ $request->routeIs('admin.rider.*') ? 'active' : '' }}"
        href="{{ route('admin.rider.index') }}">
        <span>
            <i class="bi bi-bicycle menu-icon"></i>
            {{ __('Riders') }}
        </span>
    </a>
</li>
@endhasPermission



@hasPermission(['admin.customer.index'])
<!--- customers --->
<li>
    <a class="menu {{ $request->routeIs('admin.customer.*') ? 'active' : '' }}"
        href="{{ route('admin.customer.index') }}">
        <span>
            <i class="fa-solid fa-user menu-icon"></i>
            {{ __('Customers') }}
        </span>
    </a>
</li>
@endhasPermission

@endif

@hasPermission(['admin.employee.index'])
<!--- employee --->
<li>
    <a class="menu {{ $request->routeIs('admin.employee.*') ? 'active' : '' }}"
        href="{{ route('admin.employee.index') }}">
        <span>
            <i class="fa-solid fa-users-gear menu-icon"></i>
            {{ __('Employees') }}
        </span>
    </a>
</li>
@endhasPermission

@if(session('dashboard_type') === 'ecommerce')


<!--
            
@hasPermission(['admin.role.index'])
    <!--- 
    <li>
        <a class="menu {{ $request->routeIs('admin.role.*') ? 'active' : '' }}"
            href="{{ route('admin.role.index') }}">
            <span>
                <i class="fa-solid fa-key menu-icon"></i>
                {{ __('Roles & Permissions') }}
            </span>
        </a>
    </li>
            -->

<!--
            <li>
    <a class="menu {{ request()->is('admin/role-permission/add-permission') ? 'active' : '' }}"
        href="{{ url('admin/role-permission/add-permission') }}">
        <span>
            <i class="fa-solid fa-plus-circle menu-icon"></i>
            {{ __('Add New Permission') }}
        </span>
    </a>
</li>
        
        -->

@endhasPermission

<!--- 
@hasPermission([
    'admin.generale-setting.index',
    'admin.business-setting.index',
    'admin.socialLink.index',
    'admin.themeColor.index',
    'admin.deliveryCharge.index',
    'admin.ticketissuetype.index',
    'admin.legalpage.index',
    'admin.contactUs.index',
    'admin.pusher.index',
    'admin.mailConfig.index',
    'admin.paymentGateway.index',
    'admin.sms-gateway.index',
    'admin.firebase.index',
    'admin.verification.index'
])
    <li class="menu-divider">
        <span class="menu-title">{{ __('Business Administration') }}</span>
    </li>
@endhasPermission

@hasPermission(['admin.generale-setting.index', 'admin.business-setting.index', 'admin.socialLink.index', 'admin.themeColor.index', 'admin.deliveryCharge.index', 'admin.ticketissuetype.index', 'admin.verification.index', 'admin.vatTax.index'])
     
    <li>
        <a class="menu {{ request()->routeIs('admin.generale-setting.*', 'admin.business-setting.*', 'admin.socialLink.*', 'admin.themeColor.*', 'admin.deliveryCharge.*', 'admin.ticketissuetype.*', 'admin.verification.*', 'admin.vatTax.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#setings">
            <span>
                <i class="bi bi-gear-fill menu-icon"></i>
                {{ __('Buisness Settings') }}
            </span>
            <img src="{{ asset('assets/icons/arrowDown.svg') }}" alt="" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.generale-setting.*', 'admin.business-setting.*', 'admin.socialLink.*', 'admin.themeColor.*', 'admin.deliveryCharge.*', 'admin.ticketissuetype.*', 'admin.verification.*', 'admin.vatTax.*') ? 'show' : '' }}"
            id="setings">
            <div class="listBar">
                @hasPermission('admin.generale-setting.index')
                    <a href="{{ route('admin.generale-setting.index') }}"
                        class="subMenu {{ request()->routeIs('admin.generale-setting.index') ? 'active' : '' }}">
                        {{ __('General Settings') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.business-setting.index')
                    <a href="{{ route('admin.business-setting.index') }}"
                        class="subMenu {{ request()->routeIs('admin.business-setting.*') ? 'active' : '' }}">
                        {{ __('Business Setup') }}
                    </a>
                @endhasPermission

                {{-- @hasPermission('admin.verification.index')
                    <a href="{{ route('admin.verification.index') }}"
                        class="subMenu {{ request()->routeIs('admin.verification.*') ? 'active' : '' }}">
                        {{ __('Verification') }}
                    </a>
                @endhasPermission --}}

                @hasPermission('admin.deliveryCharge.index')
                    <a href="{{ route('admin.deliveryCharge.index') }}"
                        class="subMenu {{ request()->routeIs('admin.deliveryCharge.*') ? 'active' : '' }}">
                        {{ __('Delivery Charge') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.vatTax.index')
                    <a href="{{ route('admin.vatTax.index') }}"
                        class="subMenu {{ request()->routeIs('admin.vatTax.*') ? 'active' : '' }}">
                        {{ __('VAT & Tax') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.themeColor.index')
                    <a href="{{ route('admin.themeColor.index') }}"
                        class="subMenu {{ request()->routeIs('admin.themeColor.*') ? 'active' : '' }}">
                        {{ __('Theme Colors') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.socialLink.index')
                    <a href="{{ route('admin.socialLink.index') }}"
                        class="subMenu {{ request()->routeIs('admin.socialLink.index') ? 'active' : '' }}">
                        {{ __('Social Links') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.ticketissuetype.index')
                    <a href="{{ route('admin.ticketissuetype.index') }}"
                        class="subMenu {{ request()->routeIs('admin.ticketissuetype.index') ? 'active' : '' }}">
                        {{ __('Ticket Issue Types') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

                    @endif
@use(App\Models\LegalPage)
                    
                    @if(session('dashboard_type') === 'ecommerce')

                    
                   
        
@hasPermission(['admin.legalpage.index', 'admin.contactUs.index'])

                    
                    <li>
        <a class="menu {{ request()->routeIs('admin.legalpage.*', 'admin.contactUs.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#legalPages">
            <span>
                <i class="fa-solid fa-bookmark menu-icon"></i>
                {{ __('Legal Pages') }}
            </span>
            <img src="{{ asset('assets/icons/arrowDown.svg') }}" alt="" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.legalpage.*', 'admin.contactUs.*') ? 'show' : '' }}" id="legalPages">
            <div class="listBar">
                @foreach (LegalPage::all() as $legalPage)
                    <a href="{{ route('admin.legalpage.index', $legalPage->slug) }}"
                        class="subMenu {{ request()->fullUrl() === route('admin.legalpage.edit', $legalPage->slug) || request()->fullUrl() === route('admin.legalpage.index', $legalPage->slug) ? 'active' : '' }}"
                        title="{{ $legalPage->title }}">
                        {{ __($legalPage->title) }}
                    </a>
                @endforeach

                @hasPermission('admin.contactUs.index')
                    <a href="{{ route('admin.contactUs.index') }}"
                        class="subMenu {{ request()->routeIs('admin.contactUs.*') ? 'active' : '' }}">
                        {{ __('Contact Us') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

@hasPermission(['admin.pusher.index', 'admin.mailConfig.index', 'admin.paymentGateway.index', 'admin.sms-gateway.index', 'admin.firebase.index', 'admin.googleReCaptcha.index'])
    <li>
        <a class="menu {{ request()->routeIs('admin.pusher.*', 'admin.mailConfig.*', 'admin.paymentGateway.*', 'admin.sms-gateway.*', 'admin.firebase.*'), 'admin.googleReCaptcha.*' ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#thirdpartConfig" title="Third Party configuration">
            <span>
                <i class="bi bi-boxes menu-icon"></i>
                {{ __('3rd Party Configaration') }}
            </span>
            <img src="{{ asset('assets/icons/arrowDown.svg') }}" alt="" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.pusher.*', 'admin.mailConfig.*', 'admin.paymentGateway.*', 'admin.sms-gateway.*', 'admin.firebase.*', 'admin.googleReCaptcha.*') ? 'show' : '' }}"
            id="thirdpartConfig">
            <div class="listBar">
                @hasPermission('admin.paymentGateway.index')
                    <a href="{{ route('admin.paymentGateway.index') }}"
                        class="subMenu {{ request()->routeIs('admin.paymentGateway.*') ? 'active' : '' }}">
                        {{ __('Payment Gateway') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.sms-gateway.index')
                    <a href="{{ route('admin.sms-gateway.index') }}"
                        class="subMenu {{ request()->routeIs('admin.sms-gateway.*') ? 'active' : '' }}">
                        {{ __('SMS Gateway') }}
                    </a>
                @endhasPermission

                {{-- @hasPermission('admin.socialAuth.index')
                    <a href="{{ route('admin.socialAuth.index') }}"
                        class="subMenu {{ request()->routeIs('admin.socialAuth.*') ? 'active' : '' }}">
                        {{ __('Social Authentication') }}
                    </a>
                @endhasPermission --}}

                @hasPermission('admin.pusher.index')
                    <a href="{{ route('admin.pusher.index') }}"
                        class="subMenu {{ request()->routeIs('admin.pusher.*') ? 'active' : '' }}">
                        {{ __('Pusher Setup') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.mailConfig.index')
                    <a href="{{ route('admin.mailConfig.index') }}"
                        class="subMenu {{ request()->routeIs('admin.mailConfig.*') ? 'active' : '' }}">
                        {{ __('Mail Config') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.firebase.index')
                    <a href="{{ route('admin.firebase.index') }}"
                        class="subMenu {{ request()->routeIs('admin.firebase.*') ? 'active' : '' }}">
                        {{ __('Firebase Notification') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.googleReCaptcha.index')
                    <a href="{{ route('admin.googleReCaptcha.index') }}"
                        class="subMenu {{ request()->routeIs('admin.googleReCaptcha.*') ? 'active' : '' }}">
                        {{ __('Google ReCaptcha') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission
                    --->

@if ($businessModel == 'multi')
@hasPermission(['admin.withdraw.index'])
<li class="menu-divider">
    <span class="menu-title">{{ __('Accounts') }}</span>
</li>
<!--- withdraw --->
<li>
    <a class="menu {{ $request->routeIs('admin.withdraw.*') ? 'active' : '' }}"
        href="{{ route('admin.withdraw.index') }}">
        <span>
            <i class="bi bi-wallet2 menu-icon"></i>
            {{ __('Withdraws') }}
        </span>
    </a>
</li>
@endhasPermission
@endif

<!--
@hasPermission(['admin.language.index'])
    <li class="menu-divider">
        <span class="menu-title">{{ __('Language Settings') }}</span>
    </li>
    <li>
        <a href="{{ route('admin.language.index') }}"
            class="menu {{ request()->routeIs('admin.language.*') ? 'active' : '' }}">
            <span>
                <i class="fa-solid fa-language menu-icon"></i>
                {{ __('Languages') }}
            </span>
        </a>
    </li>
@endhasPermission
-->

@if ($businessModel != 'single')
@hasPermission(['shop.profile.index'])
<li class="menu-divider">
    <span class="menu-title">{{ __('STORE MANAGEMENT') }}</span>
</li>
<!--- Profile --->
<li>
    <a class="menu {{ $request->routeIs('shop.profile.*') ? 'active' : '' }}"
        href="{{ route('shop.profile.index') }}">
        <span>
            <i class="bi bi-person-circle menu-icon"></i>
            {{ __('Shop Profile') }}
        </span>
    </a>
</li>
@endhasPermission
@endif

<!--- Import / Export --->
@hasPermission(['shop.bulk-product-import.index', 'shop.bulk-product-export.index', 'shop.gallery.index'])
<li class="menu-divider">
    <span class="menu-title">{{ __('Import / Export') }}</span>
</li>
@endhasPermission

@hasPermission('shop.bulk-product-export.index')
<li>
    <a class="menu {{ $request->routeIs('shop.bulk-product-export.*') ? 'active' : '' }}"
        href="{{ route('shop.bulk-product-export.index') }}">
        <span>
            <i class="fa-solid fa-download menu-icon"></i>
            {{ __('Bulk Export') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('shop.bulk-product-import.index')
<li>
    <a class="menu {{ $request->routeIs('shop.bulk-product-import.*') ? 'active' : '' }}"
        href="{{ route('shop.bulk-product-import.index') }}">
        <span>
            <i class="fa-solid fa-upload menu-icon"></i>
            {{ __('Bulk Import') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission('shop.gallery.index')
<!--- gallery --->
<li>
    <a class="menu {{ $request->routeIs('shop.gallery.*') ? 'active' : '' }}"
        href="{{ route('shop.gallery.index') }}">
        <span>
            <i class="fa-solid fa-file-image menu-icon"></i>
            {{ __('Gallery Import') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission(['admin.supportTicket.index', 'admin.support.index'])
<li class="menu-divider">
    <span class="menu-title">{{ __('Assistance/ Support') }}</span>
</li>
@endhasPermission
@hasPermission(['admin.supportTicket.index'])
<!--- Help Requests --->
<li>
    <a href="{{ route('admin.supportTicket.index') }}"
        class="menu {{ request()->routeIs('admin.supportTicket.*') ? 'active' : '' }}">
        <span>
            <i class="bi bi-ticket-perforated menu-icon"></i>
            {{ __('Help Requests') }}
        </span>
    </a>
</li>
@endhasPermission

@hasPermission(['admin.support.index'])
<!--- Help Notes --->
<li>
    <a href="{{ route('admin.support.index') }}"
        class="menu {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
        <span>
            <i class="bi bi-chat-right-text menu-icon"></i>
            {{ __('Help Notes') }}
        </span>
    </a>
</li>
@endhasPermission
@endif