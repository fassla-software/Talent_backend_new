@php
    $directory = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $directory }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- App favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ $generaleSetting?->favicon ?? asset('assets/favicon.png') }}" />

    <!-- App title -->
    <title>{{ $generaleSetting?->title ?? config('app.name', 'Laravel') }}</title>

    <!-- Meta -->
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    <!-- Font-Awesome--Min-Css-Link -->
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">

    <!-- sweetalert css-->
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">

    <!-- table sorter stylesheet-->
    <link rel="stylesheet" href="{{ asset('assets/css/datatables.min.css') }}">

    <!-- Bootstrap--Min-Css-Link -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">

    <!-- quill css -->
    <link rel="stylesheet" href="{{ asset('assets/css/quill.snow.css') }}">

    <!-- Custom--Css-Link -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <!-- Apexcharts Link -->
    <link rel="stylesheet" href="{{ asset('assets/css/apexcharts.css') }}">

    <!--Responsive--Css-Link -->
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">

    <!-- Toastr Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('assets/css/jquery.timepicker.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}" type="text/css">

    @stack('css')

    <style>
        .has-passport.fixed-header .app-header {
            top: 55px;
        }

        .has-passport.fixed-sidebar .app-main .app-main-outer {
            padding-top: 50px;
        }

        .has-passport.fixed-sidebar .app-sidebar {
            top: 80px;
            height: 100svh;
        }
    </style>
</head>

<body>
    <!-- alert for seeder and passport install and storage link -->
    <div class="w-100 d-flex flex-column gap-1" style="z-index: 99; position: fixed; top: 0;">
        <!-- seeder run -->
        @if ($seederRun)
            <div class="alert alert-danger alert-dismissible fade show mb-0 w-100 text-center rounded-0 text-black"
                role="alert" style="padding: 10px">
                <strong><i class="fa fa-exclamation-circle" data-toggle="tooltip" data-placement="bottom"
                        title='If you do not run this seeder, you will not be able to use the system.'></i>
                    Seeder dose not run.</strong> Please run <code class="text-danger">php artisan migrate:fresh
                    --seed</code> or <a href="{{ route('seeder.run.index') }}" class="btn btn-sm common-btn"> Click
                    here</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                    id="closeAlert"></button>
            </div>
        @endif

        <!-- storage link -->
        @if ($storageLink)
            <div class="alert alert-danger alert-dismissible fade show mb-0 w-100 text-center rounded-0 text-black"
                role="alert" style="padding: 10px">
                <strong><i class="fa fa-exclamation-circle" data-toggle="tooltip" data-placement="bottom"
                        title='If you can not install storage link, then image not found.'></i>
                    Storage link dose not exist or image not found then</strong> please run <code
                    class="text-danger">php artisan
                    storage:link</code> or <a href="{{ route('storage.install.index') }}"
                    class="btn btn-sm common-btn">
                    Click here</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                    id="closeAlert"></button>
            </div>
        @endif
    </div>

    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header {{ $seederRun || $storageLink ? 'has-passport' : '' }}"
        id="appContent">
        <div class="app-header header-shadow">
            <div class="app-header-logo"></div>
            <div class="app-header-mobile-menu">
                <div>
                    <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="app-header-menu">
                <span>
                    <button type="button"
                        class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                        <span class="btn-icon-wrapper">
                            <i class="fa fa-ellipsis-v fa-w-6"></i>
                        </span>
                    </button>
                </span>
            </div>
            <div class="app-header-content">
                <!-- Header-left-Section -->
                <div class="app-header-left">
                    <div class="header-pane ">
                        <div>
                            <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                                data-class="closed-sidebar">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- End-Header-Left-section -->

                <!-- Header-Rignt-Section -->
                <div class="app-header-right">

                    @if ($businessModel == 'multi')
                        @php
                            $user = auth()->user();
                            $isShop = true;
                            if ($user && !$user->hasRole('root') && ($user->shop || $user->myShop)) {
                                $isShop = false;
                            }
                        @endphp

                    @endif

                    @hasPermission(['admin.dashboard.notification', 'shop.dashboard.notification'])
                        <!-- Notification Section -->
                        <div class="badgeButtonBox me-3">
                            <div class="notifactionIcon">
                                <button type="button" class="emailBadge dropdown-toggle position-relative"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-bell noti"></i>
                                    <span class="position-absolute notificationCount" id="totalNotify"></span>
                                </button>
                                <div class="dropdown-menu p-0 emailNotifactionSection">
                                    <div class="dropdown-item emailNotifaction">
                                        <div class="emailHeader">
                                            <h6 class="massTitel">
                                                {{ __('Notifications') }}
                                            </h6>
                                            <a href="@hasPermission('admin.dashboard.notification')
                                                {{ route('admin.notification.readAll') }}
                                                @else
                                                {{ route('shop.notification.readAll') }}
                                                @endhasPermission"
                                                class="text-dark">
                                                {{ __('Marks all as read') }}
                                            </a>
                                        </div>
                                        <div class="messege-section" id="notifications">

                                        </div>
                                        <div class="emailFooter">
                                            <a href="@hasPermission('admin.dashboard.notification')
                                                {{ route('admin.notification.show') }}
                                                @else
                                                {{ route('shop.notification.show') }}
                                                @endhasPermission"
                                                class="massPera text-dark">
                                                {{ __('View All Notifications') }}
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endhasPermission

                    <!-- Language Dropdown -->
                    <div class="user-profile-box dropdown mx-3">
                        <div class="nav-profile-box dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="profile-image">
                                <a href="#">
                                    <img class="" src="{{ asset('assets/icons/language.svg') }}"
                                        alt="" />
                                </a>
                            </div>
                        </div>
                        <div class="dropdown-menu profile-item" style="width: 160px !important">
                            @foreach ($languages as $lang)
                                <a href="{{ route('change.language', 'language=' . $lang->name) }}"
                                    class="dropdown-item {{ $lang->name == app()->getLocale() ? 'language-active' : '' }}">
                                    <i class="fa fa-language mr-3"></i>
                                    {{ __($lang->title) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <!-- **** Resolve Session Problem***** -->
                    @auth
                    <div class="user-profile-box dropdown ml-3">
                        <div class="nav-profile-box dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="profile-image">
                                <img class="profilepic"
                                    src="{{ auth()->user()->thumbnail ?? asset('assets/icons/user.svg') }}"
                                    alt="profile" loading="lazy" />
                            </div>
                            <div class="profile-content">
                                <span>{{ ucfirst(auth()->user()->name) }}</span>
                                <i class="fa-solid fa-angle-down dropIcon"></i>
                            </div>
                        </div>

                        <div class="dropdown-menu profile-item ">
                            @if (request()->is('admin/*', 'admin'))
                                @hasPermission('admin.profile.index')
                                    <a href="{{ route('admin.profile.index') }}" class="dropdown-item">
                                        <i class="fa fa-user {{ $directory == 'rtl' ? 'ms-2' : 'me-2' }}"></i>
                                        {{ __('Profile') }}
                                    </a>
                                @endhasPermission
                            @else
                                @hasPermission('shop.profile.index')
                                    <a href="{{ route('shop.profile.index') }}" class="dropdown-item">
                                        <i class="fa fa-user {{ $directory == 'rtl' ? 'ms-2' : 'me-2' }}"></i>
                                        {{ __('Profile') }}
                                    </a>
                                @endhasPermission
                            @endif

                            @if (request()->is('admin/*', 'admin'))
                                @hasPermission('admin.generale-setting.index')
                                    <a href="{{ route('admin.generale-setting.index') }}" class="dropdown-item">
                                        <i
                                            class="fa fa-cog {{ $directory == 'rtl' ? 'ms-2' : 'me-2' }}"></i>{{ __('Settings') }}
                                    </a>
                                @endhasPermission

                                @hasPermission('admin.profile.change-password')
                                    <a href="{{ route('admin.profile.change-password') }}" class="dropdown-item">
                                        <i
                                            class="fa-solid fa-key {{ $directory == 'rtl' ? 'ms-2' : 'me-2' }}"></i>{{ __('Change Password') }}
                                    </a>
                                @endhasPermission
                            @else
                                @hasPermission('shop.profile.change-password')
                                    <a href="{{ route('shop.profile.change-password') }}" class="dropdown-item">
                                        <i
                                            class="fa-solid fa-key {{ $directory == 'rtl' ? 'ms-2' : 'me-2' }}"></i>{{ __('Change Password') }}
                                    </a>
                                @endhasPermission
                            @endif

                            <button class="dropdown-item cursor-pointer logout">
                                <i
                                    class="fa-solid fa-right-from-bracket {{ $directory == 'rtl' ? 'ms-2' : 'me-2' }}"></i>
                                {{ __('Logout') }}
                            </button>
                        </div>
                    </div>
                    <!-- **** Resolve Session Problem***** -->
                    @else
                    <script>
                        window.location.replace('{{ route('admin.login') }}');
                    </script>
                    @endauth
                    
                </div>
                <!-- End-Header-Right-Section -->

            </div>
        </div>
        <div class="app-main">

            @include('layouts.sidebar')

            <!-- ****Body-Section***** -->

            <div class="app-main-outer">
                <!-- ****End-Body-Section**** -->
                <div class="app-main-inner">
                    <div class="container-fluid">

                        @yield('content')

                    </div>
                </div>
                <!-- Footer-Section -->

                @if (!$generaleSetting || $generaleSetting?->show_footer)
                    <div class="app-wrapper-footer">
                        <div class="app-footer">
                            <div class="app-footer-inner">
                                <div>
                                    © {{ date('Y') }} {{ $generaleSetting?->footer_text }}
                                </div>
                                <div class="d-none d-sm-block" style="display: none !important;">
    <i class="bi bi-telephone"></i>
    <span>
        {{ $generaleSetting?->footer_phone ?? '+8801714231625' }}
    </span>
</div>

                                <div class="d-none d-sm-block">
                                    <i class="fa-solid fa-envelope"></i>
                                    <span>
                                        {{ $generaleSetting?->footer_email ?? 'razinsoftltd@gmail.com' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Logout Form -->
    @php
        $action = request()->is('admin/*', 'admin') ? route('admin.logout') : route('shop.logout');
    @endphp
    <form action="{{ $action }}" method="POST" id="logoutForm">
        @csrf
    </form>

    <script src="{{ asset('assets/scripts/jquery-3.6.3.min.js') }}"></script>
    <!-- Bootstrap-Min-Bundil-Link -->
    <script src="{{ asset('assets/scripts/bootstrap.bundle.min.js') }}"></script>
    <!-- Main-Script-Js-Link -->
    <script src="{{ asset('assets/scripts/main.js') }}"></script>

    <!-- Apex-Charts-Js-Link -->
    <script src="{{ asset('assets/scripts/apexcharts.min.js') }}"></script>

    <!-- Full-Screen-Js-Link -->
    <script src="{{ asset('assets/scripts/full-screen.js') }}"></script>

    <!--select2 -->
    <script src="{{ asset('assets/scripts/select2.min.js') }}"></script>

    <!-- sweetalert js-->
    <script src="{{ asset('assets/scripts/sweetalert2.min.js') }}"></script>
    
    <script src="{{ asset('assets/scripts/datatables.min.js') }}"></script>

    <!-- quill  editor-->
    <script src="{{ asset('assets/scripts/quill.js') }}"></script>

    <script src="{{ asset('assets/scripts/jQuery.print.min.js') }}"></script>

    <script src="{{ asset('assets/scripts/script.js') }}"></script>

    <script src="{{ asset('assets/scripts/toastr.min.js') }}"></script>

    <script src="{{ asset('assets/scripts/jquery.timepicker.min.js') }}"></script>
    <script src="{{ asset('assets/scripts/jquery-ui.js') }}"></script>

    <!-- Pusher-Js-Link -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });

        document.addEventListener("DOMContentLoaded", function() {
            var themeColor = "{{ $generaleSetting?->primary_color ?? '#EE456B' }}";
            var themeHoverColor = "{{ $generaleSetting?->secondary_color ?? '#FEE5E8' }}";
            document.documentElement.style.setProperty('--theme-color', themeColor);
            document.documentElement.style.setProperty('--theme-hover-bg', themeHoverColor);
        });

        // Fetch Admin Notifications
        const fetchAdminNotifications = () => {
            $.ajax({
                type: 'GET',
                url: "{{ route('admin.dashboard.notification') }}",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(response) {
                    $('#totalNotify').text(response.data.total)
                    $('#notifications').empty()
                    $.each(response.data.notifications, function(key, value) {
                        var id = value.id;
                        var link = "{{ route('admin.notification.read', ':id') }}";
                        link = link.replace(':id', id);
                        $('#notifications').append(
                            `<a href="${link}" class="item d-flex gap-2 align-items-center">
                            <div class="iconBox ${value.type == 'danger' ? 'cardIcon' : 'pdfIcon'}">
                                <i class="bi ${value.icon}"></i>
                            </div>
                            <div class="notification w-100 ${!value.is_read ? 'unread' : ''}">
                                <div class="userName">
                                    <p class="massTitel">${value.title} </p>
                                    <span class="time">${value.time}</span>
                                </div>
                                <div>
                                    <p class="description">${value.content}</p>
                                </div>
                            </div>
                        </a>`
                        );
                    })
                },
                error: function(e) {
                    $('#notifications').empty()
                    if (e.status == 401 || e.status == 403) {
                        $('#totalNotify').text(0)
                        $("#notifications").html("emply");
                    }else{
                        $("#notifications").html(e.responseText);
                    }
                }
            });
        }

        // fetch shop notifications
        const fetchShopNotifications = () => {
            $.ajax({
                type: 'GET',
                url: "{{ route('shop.dashboard.notification') }}",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(response) {
                    $('#totalNotify').text(response.data.total)
                    $('#notifications').empty()
                    $.each(response.data.notifications, function(key, value) {
                        var id = value.id;
                        var link = "{{ route('shop.notification.read', ':id') }}";
                        link = link.replace(':id', id);
                        $('#notifications').append(
                            `<a href="${link}" class="item d-flex gap-2 align-items-center">
                            <div class="iconBox ${value.type == 'danger' ? 'cardIcon' : 'pdfIcon'}">
                                <i class="bi ${value.icon}"></i>
                            </div>
                            <div class="notification w-100 ${!value.is_read ? 'unread' : ''}">
                                <div class="userName">
                                    <p class="massTitel">${value.title} </p>
                                    <span class="time">${value.time}</span>
                                </div>
                                <div>
                                    <p class="description">${value.content}</p>
                                </div>
                            </div>
                        </a>`
                        );
                    })
                },
                error: function(e) {
                    $('#notifications').empty()
                    if (e.status == 401 || e.status == 403) {
                        $('#totalNotify').text(0)
                        $("#notifications").html("emply");
                    }else{
                        $("#notifications").html(e.responseText);
                    }
                }
            });
        }
    </script>

    <!-- Pusher Scripts -->
    <script>
        var pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
            cluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}",
        });

        var channel = pusher.subscribe('notification-channel');
    </script>

    <!-- Show Notifications Using Pusher JS -->

    @if (request()->is('admin/*', 'admin'))
        @hasPermission('admin.dashboard.notification')
            <script>
                channel.bind('admin-product-request', function(data) {
                    var message = data.message;
                    if (message.startsWith('"') && message.endsWith('"')) {
                        message = message.slice(1, -1);
                    }
                    toastr.success(message)
                    // new Audio("{{ asset('assets/audio/notification.mp3') }}").play();
                    fetchAdminNotifications()
                });

                channel.bind('support-ticket-event', function(data) {
                    var message = data.message;
                    if (message.startsWith('"') && message.endsWith('"')) {
                        message = message.slice(1, -1);
                    }
                    toastr.success(message)
                    fetchAdminNotifications()
                });

                fetchAdminNotifications() // fetch Admin Notifications
            </script>
        @endhasPermission
    @else
        @hasPermission('shop.dashboard.notification')
            <script>
                var shopID = "{{ generaleSetting('shop')?->id }}";
                channel.bind('product-approve-event', function(data) {
                    var requestShopId = data.shop_id;
                    var message = data.message;
                    if (requestShopId == shopID) {
                        if (message.startsWith('"') && message.endsWith('"')) {
                            message = message.slice(1, -1);
                        }
                        toastr.success(message)
                        fetchShopNotifications()
                    }
                });
                fetchShopNotifications() // fetch Shop Notifications
            </script>
        @endhasPermission
    @endif

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    </script>

    @stack('scripts')

    @if (session('success'))
        <script>
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            })
        </script>
    @endif

    @if (session('error'))
        <script>
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            })
        </script>
    @endif

    @if (session('demoMode'))
        <script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "{{ session('demoMode') }}",
            });
        </script>
    @endif

    @if (session('alertError'))
        <script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                html: `{{ session('alertError')['message'] }} <br><br> {{ isset(session('alertError')['message2']) ? session('alertError')['message2'] : '' }}`,
            });
        </script>
    @endif

    <Script>
        document.addEventListener("DOMContentLoaded", function() {
            var root = document.documentElement;

            // Get the value of --theme-color
            var themeColor = getComputedStyle(root).getPropertyValue("--theme-color");
        
$('.delete-form').on('submit', function(e) {
    e.preventDefault();  // Prevent the form from submitting immediately
    var button = $(this).find('button[type="submit"]'); // Get the submit button inside the form
    
    // Store the original button content (icon)
    var originalContent = button.html(); 
    
    // Change the button content to a loading state (a spinner or similar)
    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');  
    
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with the AJAX request to delete the item
            $.ajax({
                url: button.closest('form').data('route'),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                },
                data: {
                    _method: 'DELETE'
                },
                beforeSend: function() {
                    // Keep the button disabled and show loading indicator
                    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message || "Successfully deleted!",
                    }).then(() => {
                        window.location.href = '/admin/plumber-users';
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Something went wrong.',
                    });
                },
                complete: function() {
                    // Reset the button content and re-enable the button after AJAX completes
                    button.prop('disabled', false).html(originalContent);
                }
            });
        } else {
            // Re-enable the button and reset its content if cancel is clicked
            button.prop('disabled', false).html(originalContent);
        }
    });
});
        
$('.delete-form-category').on('submit', function(e) {
    e.preventDefault();  // Prevent the form from submitting immediately
    var button = $(this).find('button[type="submit"]'); // Get the submit button inside the form
    
    // Store the original button content (icon)
    var originalContent = button.html(); 
    
    // Change the button content to a loading state (a spinner or similar)
    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');  
    
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with the AJAX request to delete the item
            $.ajax({
                url: button.closest('form').data('route'),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                },
                data: {
                    _method: 'DELETE'
                },
                beforeSend: function() {
                    // Keep the button disabled and show loading indicator
                    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message || "Successfully deleted!",
                    }).then(() => {
                        window.location.href = '/add-edit-category';
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Something went wrong.',
                    });
                },
                complete: function() {
                    // Reset the button content and re-enable the button after AJAX completes
                    button.prop('disabled', false).html(originalContent);
                }
            });
        } else {
            // Re-enable the button and reset its content if cancel is clicked
            button.prop('disabled', false).html(originalContent);
        }
    });
});

$('.delete-form-withdraw').on('submit', function(e) {
    e.preventDefault();  // Prevent the form from submitting immediately
    var button = $(this).find('button[type="submit"]'); // Get the submit button inside the form
    
    // Store the original button content (icon)
    var originalContent = button.html(); 
    
    // Change the button content to a loading state (a spinner or similar)
    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');  
    
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with the AJAX request to delete the item
            $.ajax({
                url: button.closest('form').data('route'),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                },
                data: {
                    _method: 'DELETE'
                },
                beforeSend: function() {
                    // Keep the button disabled and show loading indicator
                    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message || "Successfully deleted!",
                    }).then(() => {
                        window.location.href = '/withdraw';
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Something went wrong.',
                    });
                },
                complete: function() {
                    // Reset the button content and re-enable the button after AJAX completes
                    button.prop('disabled', false).html(originalContent);
                }
            });
        } else {
            // Re-enable the button and reset its content if cancel is clicked
            button.prop('disabled', false).html(originalContent);
        }
    });
});

$('.delete-form-gift').on('submit', function(e) {
    e.preventDefault();  // Prevent the form from submitting immediately
    var button = $(this).find('button[type="submit"]'); // Get the submit button inside the form
    
    // Store the original button content (icon)
    var originalContent = button.html(); 
    
    // Change the button content to a loading state (a spinner or similar)
    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');  
    
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with the AJAX request to delete the item
            $.ajax({
                url: button.closest('form').data('route'),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                },
                data: {
                    _method: 'DELETE'
                },
                beforeSend: function() {
                    // Keep the button disabled and show loading indicator
                    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: response.message || "Successfully deleted!",
                    }).then(() => {
                        window.location.href = '/gift/list';
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Something went wrong.',
                    });
                },
                complete: function() {
                    // Reset the button content and re-enable the button after AJAX completes
                    button.prop('disabled', false).html(originalContent);
                }
            });
        } else {
            // Re-enable the button and reset its content if cancel is clicked
            button.prop('disabled', false).html(originalContent);
        }
    });
});        
        
            $(".deleteConfirm").on("click", function(e) {
                e.preventDefault();
                const url = $(this).attr("href");
                Swal.fire({
                    title: "{{ __('Are you sure?') }}",
                    text: '{{ __('You will not be able to revert this!') }}',
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: themeColor,
                    cancelButtonColor: "#d33",
                    confirmButtonText: "{{ __('Yes, delete it!') }}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });

            $(".logout").on("click", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ __('Are you sure?') }}",
                    text: "{{ __('Are you sure you want to log out?') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: themeColor,
                    cancelButtonColor: "#d33",
                    confirmButtonText: "{{ __('Yes, Logout!') }}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById("logoutForm").submit();
                    }
                });
            });

            // form submit loader
            $('form').on('submit', function() {
                var submitButton = $(this).find('button[type="submit"]');

                submitButton.prop('disabled', true);
                submitButton.removeClass('px-5');

                submitButton.html(`<div class="d-flex align-items-center gap-1">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <span>Loading...</span>
                </div>`)
            });
        });
    </Script>

</body>

</html>