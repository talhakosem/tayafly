<!DOCTYPE html>
<html lang="tr">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        $settings = \App\Models\Setting::getSettings();
    @endphp
    
    <title>@yield('title', 'Ana Sayfa') - {{ $settings->site_title ?? config('app.name', 'Fidanlık') }}</title>
    
    <!-- Meta Description -->
    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
    @else
        <meta name="description" content="{{ $settings->site_description ?? '' }}">
    @endif
    
    <!-- Meta Keywords -->
    @if($settings->site_keywords)
        <meta name="keywords" content="{{ $settings->site_keywords }}">
    @endif
    
    <!-- Favicon -->
    @if($settings->favicon)
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $settings->favicon) }}">
    @else
        <link rel="shortcut icon" type="image/x-icon" href="{{ frontend_asset('images/favicon/favicon.ico') }}">
    @endif
    
    <!-- Libs CSS -->
    <link href="{{ frontend_asset('libs/bootstrap-icons/font/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ frontend_asset('libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ frontend_asset('libs/drift-zoom/dist/drift-basic.min.css') }}" rel="stylesheet">
    <link href="{{ frontend_asset('libs/simplebar/dist/simplebar.min.css') }}" rel="stylesheet">
    
           <!-- Theme CSS -->
           <link rel="stylesheet" href="{{ frontend_asset('css/theme.min.css') }}">

           <!-- Mega Menu Styles -->
           <style>
               .nav-item.dropdown {
                   position: static;
               }
               
               .dropdown-menu-mega {
                   position: fixed;
                   left: 0;
                   right: 0;
                   width: 100%;
                   max-width: 100%;
                   margin: 0 auto;
                   top: auto;
                   border-top: 1px solid #dee2e6;
                   padding-top: 0 !important;
                   margin-top: 0 !important;
               }
               
               /* Navbar'ın altına hizala */
               .navbar-expand-xl .navbar-nav .nav-item.dropdown:hover .dropdown-menu-mega {
                   top: 100%;
                   margin-top: 0;
               }
               
               /* Hover durumunu hem nav-item hem de dropdown için kontrol et */
               .nav-item.dropdown:hover .dropdown-menu-mega,
               .dropdown-menu-mega:hover {
                   display: block !important;
               }
               
               .nav-item.dropdown .dropdown-menu-mega {
                   display: none;
               }
               
               /* Dropdown ile nav-item arasındaki boşluğu kaldır - invisible bridge */
               .dropdown-menu-mega::before {
                   content: '';
                   position: absolute;
                   top: -20px;
                   left: 0;
                   right: 0;
                   height: 20px;
                   background: transparent;
               }
               
               /* Nav-item'ın tamamı hover alanı olsun */
               .nav-item.dropdown:hover {
                   z-index: 1000;
               }
               
               .dropdown-menu-mega {
                   z-index: 999;
               }
               
               /* Navbar'ın altına tam hizalama için */
               .navbar {
                   position: relative;
               }
               
               @media (min-width: 1200px) {
                   .dropdown-menu-mega {
                       left: 50%;
                       transform: translateX(-50%);
                   }
               }
               
               @media (min-width: 1200px) {
                   .dropdown-menu-mega {
                       left: 50%;
                       transform: translateX(-50%);
                   }
               }
               
               .dropdown-menu-mega .container {
                   max-width: 1200px;
               }
               
               .dropdown-menu-mega a:hover {
                   color: #0d6efd !important;
               }
           </style>

           <!-- Additional CSS -->
           @stack('styles')
</head>
<body>
    <!-- Header/Navbar -->
    @include('frontend.partials.header', ['settings' => $settings])
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('frontend.partials.footer', ['settings' => $settings])
    
    <!-- Libs JS -->
    <script src="{{ frontend_asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ frontend_asset('libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ frontend_asset('libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ frontend_asset('libs/drift-zoom/dist/Drift.min.js') }}"></script>
    
    <!-- Theme JS -->
    <script src="{{ frontend_asset('js/theme.min.js') }}"></script>
    
    <!-- Vendor JS -->
    <script src="{{ frontend_asset('js/vendors/swiper.js') }}"></script>
    <script src="{{ frontend_asset('js/vendors/drift.js') }}"></script>
    <script src="{{ frontend_asset('js/vendors/slide-hint-img.js') }}"></script>
    <script src="{{ frontend_asset('js/vendors/flag.js') }}"></script>
    <script src="{{ frontend_asset('js/vendors/color-change.js') }}"></script>
    <script src="{{ frontend_asset('js/vendors/add-to-cart.js') }}"></script>
    <script src="{{ frontend_asset('js/vendors/qty-input.js') }}"></script>
    <script src="{{ frontend_asset('js/vendors/btn-scrolltop.js') }}"></script>
    
    <!-- Additional JS -->
    @stack('scripts')
</body>
</html>

