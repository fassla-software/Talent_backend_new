<div class="app-sidebar">
    <div class="scrollbar-sidebar">
        <div class="branding-logo">
            @php
                $request = request();

                $shop = generaleSetting('shop');
                $rootShop = generaleSetting('rootShop');
                $isAdmin = $shop->id == $rootShop->id ? true : false;

                $url = $isAdmin ? route('admin.dashboard.index') : route('shop.dashboard.index');
            @endphp
            <a href="{{ $url }}">
                <img src="{{ $generaleSetting?->logo ?? asset('assets/logo.png') }}" alt="logo" loading="lazy" />
            </a>
        </div>
        <div class="branding-logo-forMobile">
            <a href="{{ $generaleSetting?->logo ?? asset('assets/logo.png') }}"></a>
        </div>
        <div class="app-sidebar-inner">
            <ul class="vertical-nav-menu">
                @if ($isAdmin)
                    @include('layouts.partials.admin-menu')
                @else
                    @include('layouts.partials.shop-menu')
                @endif
            </ul>
        </div>
        <div class="sideBarfooter">
            <button type="button" class="fullbtn hite-icon" onclick="toggleFullScreen(document.body)">
                <i class="fa-solid fa-expand"></i>
            </button>
            @if ($isAdmin)
                @hasPermission('admin.generale-setting.index')
                <a href="{{ route('admin.generale-setting.index') }}" class="fullbtn hite-icon">
                    <i class="fa-solid fa-cog"></i>
                </a>
                @endhasPermission
            @endif
            <a href="#" class="fullbtn hite-icon">
                <i class="fa-solid fa-user"></i>
            </a>
            <a href="javascript:void(0)" class="fullbtn hite-icon logout">
                <i class="fa-solid fa-power-off"></i>
            </a>
        </div>
    </div>
</div>
