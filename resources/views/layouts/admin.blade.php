<!DOCTYPE html>
<html lang="tr">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Codescandy" name="author">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $settings = \App\Models\Setting::getSettings();
    @endphp
    
    <title>@yield('title', 'Admin Panel') - {{ $settings->site_title ?? config('app.name', 'FreshCart') }}</title>
    
    <!-- Favicon icon-->
    @if($settings->favicon)
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $settings->favicon) }}">
    @else
        <link rel="shortcut icon" type="image/x-icon" href="{{ admin_asset('images/favicon/favicon.ico') }}">
    @endif

    <!-- Libs CSS -->
    <link href="{{ admin_asset('libs/bootstrap-icons/font/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ admin_asset('libs/feather-webfont/dist/feather-icons.css') }}" rel="stylesheet">
    <link href="{{ admin_asset('libs/simplebar/dist/simplebar.min.css') }}" rel="stylesheet">

    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ admin_asset('css/theme.min.css') }}">
    
    <!-- Additional CSS -->
    @stack('styles')
</head>

<body>
    <!-- main -->
    <div>
        <div class="main-wrapper">
            <!-- navbar vertical -->
            <nav class="navbar-vertical-nav d-none d-xl-block">
                <div class="navbar-vertical">
                    <div class="px-4 py-5">
                        <a href="{{ route('dashboard') }}" class="navbar-brand">
@if($settings->logo)
                                <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" style="max-height: 40px;" />
                            @else
                                @if($settings->logo)
                                <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" style="max-height: 40px;" />
                            @else
                                <img src="{{ admin_asset('images/logo/freshcart-logo.svg') }}" alt="Logo" />
                            @endif
                            @endif
                        </a>
                    </div>
                    <div class="navbar-vertical-content flex-grow-1" data-simplebar="">
                        <ul class="navbar-nav flex-column" id="sideNavbar">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><i class="bi bi-house"></i></span>
                                        <span class="nav-link-text">Dashboard</span>
                                    </div>
                                </a>
                            </li>
                            
                            <li class="nav-item mt-6 mb-3">
                                <span class="nav-label">Blog Yönetimi</span>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('posts.*') ? 'active' : '' }}" href="{{ route('posts.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><i class="bi bi-journal-text"></i></span>
                                        <span class="nav-link-text">Blog Yazıları</span>
                                    </div>
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><i class="bi bi-list-task"></i></span>
                                        <span class="nav-link-text">Kategoriler</span>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item mt-6 mb-3">
                                <span class="nav-label">Ayarlar</span>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><i class="bi bi-gear"></i></span>
                                        <span class="nav-link-text">Site Ayarları</span>
                                    </div>
                                </a>
                            </li>


                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Mobile navbar -->
            <nav class="navbar-vertical-nav offcanvas offcanvas-start navbar-offcanvac" tabindex="-1" id="offcanvasExample">
                <div class="navbar-vertical">
                    <div class="px-4 py-5 d-flex justify-content-between align-items-center">
                        <a href="{{ route('dashboard') }}" class="navbar-brand">
@if($settings->logo)
                                <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" style="max-height: 40px;" />
                            @else
                                @if($settings->logo)
                                <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" style="max-height: 40px;" />
                            @else
                                <img src="{{ admin_asset('images/logo/freshcart-logo.svg') }}" alt="Logo" />
                            @endif
                            @endif
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="navbar-vertical-content flex-grow-1" data-simplebar="">
                        <ul class="navbar-nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><i class="bi bi-house"></i></span>
                                        <span>Dashboard</span>
                                    </div>
                                </a>
                            </li>
                    
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('posts.*') ? 'active' : '' }}" href="{{ route('posts.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><i class="bi bi-journal-text"></i></span>
                                        <span class="nav-link-text">Blog Yazıları</span>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><i class="bi bi-list-task"></i></span>
                                        <span class="nav-link-text">Kategoriler</span>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><i class="bi bi-gear"></i></span>
                                        <span class="nav-link-text">Site Ayarları</span>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- main wrapper -->
            <main class="main-content-wrapper">
                <!-- Navbar Top -->
                <nav class="navbar navbar-expand-lg navbar-glass">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="d-flex align-items-center">
                                <a class="text-inherit d-block d-xl-none me-4" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-text-indent-right" viewBox="0 0 16 16">
                                        <path d="M2 3.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm10.646 2.146a.5.5 0 0 1 .708.708L11.707 8l1.647 1.646a.5.5 0 0 1-.708.708l-2-2a.5.5 0 0 1 0-.708l2-2zM2 6.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z" />
                                    </svg>
                                </a>

                                <form role="search">
                                    <label for="search" class="form-label visually-hidden">Ara</label>
                                    <input class="form-control" type="search" placeholder="Ara" aria-label="Ara" id="search" />
                                </form>
                            </div>
                            <div>
                                <ul class="list-unstyled d-flex align-items-center mb-0 ms-5 ms-lg-0">
                                    <li class="dropdown ms-4">
                                        <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            @if($settings->favicon)
                                                <img src="{{ asset('storage/' . $settings->favicon) }}" alt="Favicon" class="avatar avatar-md rounded-circle" />
                                            @else
                                                <img src="{{ admin_asset('images/favicon/favicon.ico') }}" alt="Favicon" class="avatar avatar-md rounded-circle" />
                                            @endif
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-end p-0">
                                            <div class="border-top px-5 py-3">
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-link p-0 text-decoration-none">Logout</button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <section class="container">
                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Başarılı!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Hata!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Doğrulama Hataları:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @yield('content')
                </section>
            </main>
        </div>
    </div>

    <!-- Libs JS -->
    <script src="{{ admin_asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ admin_asset('libs/simplebar/dist/simplebar.min.js') }}"></script>

    <!-- Theme JS -->
    <script src="{{ admin_asset('js/theme.min.js') }}"></script>

    <!-- Additional JS -->
    @stack('scripts')
</body>
</html>

