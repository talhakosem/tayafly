{{-- Top Alert Banner --}}
@if($settings->site_top_text)
<div class="alert alert-dismissible fade show p-0 mb-0 border-0" role="alert">
    <div class="bg-dark position-relative z-1">
        <div class="container">
            <div class="row">
                <div class="offset-lg-3 col-lg-6 col-11">
                    <div class="swiper-container swiper" id="swiper-top" data-pagination-type="" data-speed="2000"
                        data-space-between="100" data-pagination="false" data-navigation="true" data-autoplay="true"
                        data-effect="slide" data-autoplay-delay="3000"
                        data-breakpoints='{"480": {"slidesPerView": 1}, "768": {"slidesPerView": 1}, "1024": {"slidesPerView": 1}}'>
                        <div class="swiper-wrapper text-center" style="line-height: 36px">
                            <div class="swiper-slide text-white mb-0 small">{{ $settings->site_top_text }}</div>
                            @if($settings->site_top_link)
                                <div class="swiper-slide text-white small">
                                    <a href="{{ $settings->site_top_link }}" class="text-white text-decoration-none">{{ $settings->site_top_text }}</a>
                                </div>
                            @endif
                        </div>
                        <!-- Add Navigation -->
                        <div class="swiper-navigation d-flex justify-content-between w-100">
                            <div class="swiper-button-prev me-2 btn btn-icon btn-xxs btn-white rounded-circle end-auto start-0"
                                style="top: 82%"></div>
                            <div class="swiper-button-next btn btn-icon btn-xxs btn-white rounded-circle start-auto end-0"
                                style="top: 82%"></div>
                        </div>
                    </div>
                </div>
                <div class="offset-lg-2 col-lg-1 d-flex justify-content-end align-items-center col-1">
                    <a href="#!" class="text-white" data-bs-dismiss="alert" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-x lh-1" viewBox="0 0 16 16">
                            <path
                                d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Main Navigation --}}
<nav class="navbar navbar-expand-xl sticky-top bg-white w-100 border-bottom">
    <div class="container">
        <div class="row w-100 align-items-center g-0">
            <div class="col-auto">
                <div class="d-flex align-items-center">
                    <button class="navbar-toggler offcanvas-nav-btn" type="button">
                        <i class="bi bi-list"></i>
                    </button>
                    <a class="navbar-brand ms-4" href="{{ route('home') }}">
                        @if($settings->logo)
                            <img src="{{ asset('storage/' . $settings->logo) }}" alt="{{ $settings->site_title }}" style="max-height: 50px;">
                        @else
                            <img src="{{ frontend_asset('images/logo/logo.svg') }}" alt="{{ $settings->site_title ?? 'Logo' }}" />
                        @endif
                    </a>
                </div>
            </div>
            <div class="col">
                {{-- Desktop Menu --}}
                <ul class="navbar-nav mb-2 mb-lg-0 flex-row gap-3 d-none d-xl-flex justify-content-end" style="flex-wrap: nowrap;">
                    @php
                        $topMenuCategories = \App\Models\Category::where('show_in_top_menu', true)
                            ->where(function($query) {
                                $query->whereNull('parent_id')
                                      ->orWhere('parent_id', 0);
                            })
                            ->orderBy('sort_order')
                            ->get();
                    @endphp
                    
                    @foreach($topMenuCategories as $category)
                        <li class="nav-item">
                            <a class="nav-link py-1 px-2 d-flex flex-column align-items-center {{ request()->is($category->slug) ? 'active' : '' }}" 
                               href="{{ url('/' . $category->slug) }}"
                               style="font-size: 0.8rem; white-space: nowrap; min-width: fit-content;">
                                @if($category->image)
                                    <div class="mb-1" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                        <img src="{{ asset('storage/' . $category->image) }}" 
                                             alt="{{ $category->name }}" 
                                             style="max-width: 24px; max-height: 24px; object-fit: contain;">
                                    </div>
                                @endif
                                <span>{{ $category->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                
                {{-- Mobile Menu (Offcanvas) --}}
                <div class="offcanvas offcanvas-bottom offcanvas-nav d-xl-none" style="height: 60vh">
                    <div class="offcanvas-header position-absolute top-0 start-50 translate-middle mt-n5">
                        <button type="button" class="btn-close bg-white opacity-100" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body pt-xl-0 align-items-center">
                        <ul class="navbar-nav mb-2 mb-lg-0 flex-column">
                            @php
                                $mobileMenuCategories = \App\Models\Category::where('show_in_top_menu', true)
                                    ->where(function($query) {
                                        $query->whereNull('parent_id')
                                              ->orWhere('parent_id', 0);
                                    })
                                    ->orderBy('sort_order')
                                    ->get();
                            @endphp
                            
                            @foreach($mobileMenuCategories as $category)
                                <li class="nav-item">
                                    <a class="nav-link py-1 px-2 d-flex flex-column align-items-center {{ request()->is($category->slug) ? 'active' : '' }}" 
                                       href="{{ url('/' . $category->slug) }}">
                                        @if($category->image)
                                            <div class="mb-1" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                                <img src="{{ asset('storage/' . $category->image) }}" 
                                                     alt="{{ $category->name }}" 
                                                     style="max-width: 24px; max-height: 24px; object-fit: contain;">
                                            </div>
                                        @endif
                                        <span>{{ $category->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="d-grid position-absolute bottom-0 w-100 start-0 end-0 p-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn btn-primary">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
