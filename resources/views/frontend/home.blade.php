@extends('layouts.frontend')

@section('title', 'Home')

@php
    $settings = \App\Models\Setting::getSettings();
@endphp

@section('meta_description', $settings->site_description ?? 'Home page')

@section('content')
<!--Categories Swiper Section start-->
<section class="py-lg-4 py-4">
    <div class="container">
        @php
            $topMenuCategories = \App\Models\Category::where('show_in_top_menu', true)
                ->where(function($query) {
                    $query->whereNull('parent_id')
                          ->orWhere('parent_id', 0);
                })
                ->with(['children' => function($q) {
                    $q->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get();
        @endphp
        
        @if($topMenuCategories->count() > 0)
            <div class="swiper-container swiper" id="swiper-categories" data-pagination-type="" data-speed="800" data-space-between="100"
                data-pagination="false" data-navigation="true" data-autoplay="true" data-effect="fade"
                data-autoplay-delay="3000"
                data-breakpoints='{"480": {"slidesPerView": 2}, "768": {"slidesPerView": 3}, "1024": {"slidesPerView": 1}}'>
                <div class="swiper-wrapper pb-lg-8">
                    @foreach($topMenuCategories as $category)
                        <div class="swiper-slide w-100 bg-light bg-opacity-50 border-bottom">
                            <div class="container d-flex flex-column justify-content-center h-100">
                                <div class="row align-items-center py-md-8 py-6">
                                    <div class="col-lg-5">
                                        <div class="">
                                            <h1 class="mb-3 mt-4 display-5 fw-bold">{{ $category->name }}</h1>
                                            @if($category->meta_description || $category->description)
                                                <p class="mb-4 pe-lg-6">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($category->meta_description ?? $category->description ?? ''), 200) }}
                                                </p>
                                            @endif
                                            <a href="{{ url('/' . $category->slug) }}" class="btn btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                    <div class="offset-lg-1 col-lg-6">
                                        <div class="position-relative">
                                            @php
                                                $categoryPosts = \App\Models\Post::whereHas('categories', function($query) use ($category) {
                                                    $query->where('categories.id', $category->id);
                                                })
                                                ->where('is_published', true)
                                                ->inRandomOrder()
                                                ->limit(5)
                                                ->get();
                                            @endphp
                                            
                                            @if($categoryPosts->count() > 0)
                                                <div class="bg-light p-4 rounded">
                                                    <h4 class="mb-3">Blog Posts</h4>
                                                    <div class="row g-3">
                                                        @foreach($categoryPosts as $post)
                                                            <div class="col-12">
                                                                <a href="{{ $post->url }}" class="text-decoration-none d-flex align-items-start">
                                                                    @if($post->cover_image)
                                                                        <img src="{{ asset('storage/' . $post->cover_image) }}" 
                                                                             alt="{{ $post->title }}"
                                                                             class="img-thumbnail me-3"
                                                                             style="width: 80px; height: 80px; object-fit: cover;">
                                                                    @endif
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="mb-1 text-dark">{{ \Illuminate\Support\Str::limit($post->title, 50) }}</h6>
                                                                        @if($post->short_description || $post->meta_description)
                                                                            <p class="mb-0 text-muted small">
                                                                                {{ \Illuminate\Support\Str::limit(strip_tags($post->short_description ?? $post->meta_description ?? ''), 60) }}
                                                                            </p>
                                                                        @endif
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center p-4 rounded" style="min-height: 200px;">
                                                    <p class="text-muted mb-0">No blog posts found in this category</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
                <!-- Add Navigation -->
                <div class="swiper-navigation position-absolute end-25 bottom-0 bottom-md-10 me-md-n10 mb-8">
                    <div class="swiper-button-next btn btn-icon btn-sm btn-outline-primary rounded-circle"></div>
                    <div class="swiper-button-prev me-2 btn btn-icon btn-sm btn-outline-primary rounded-circle"></div>
                </div>
            </div>
        @endif
    </div>
</section>
<!--Categories Swiper Section end-->

<!--Random Blog Posts Section start-->
@if(isset($randomPosts) && $randomPosts->count() > 0)
<section class="py-lg-2 pt-0 mx-3 mx-lg-0">
    <div class="container">
        <div class="row mb-md-8 mb-4">
            <div class="col-lg-12">
                <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-md-between gap-4">
                    <!--Heading-->
                    <div class="col-sm-7">
                        <h2>Latest Blog Posts</h2>
                        <p class="mb-0">Discover our latest blog posts.</p>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('frontend.blog.index') }}" class="d-flex align-items-center gap-2 btn-dark-link">
                            <span class="text-link">View All</span>
                            <span class="btn btn-outline-primary btn-icon btn-xxs rounded-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor"
                                    class="bi bi-chevron-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4">
        @foreach($randomPosts as $post)
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    @if($post->cover_image)
                        <a href="{{ $post->url }}">
                            <img src="{{ asset('storage/' . $post->cover_image) }}" 
                                 alt="{{ $post->title }}"
                                 class="card-img-top"
                                 style="height: 250px; object-fit: cover;">
                        </a>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ $post->url }}" class="text-decoration-none text-dark">
                                {{ $post->title }}
                            </a>
                        </h5>
                        @if($post->meta_description)
                            <p class="card-text text-muted">
                                {{ \Illuminate\Support\Str::limit(strip_tags($post->meta_description), 150) }}
                            </p>
                        @endif
                        <a href="{{ $post->url }}" class="btn btn-outline-primary btn-sm">Read More</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif
<!--Random Blog Posts Section end-->

<!--Blog Section start-->
@if(isset($randomPosts) && $randomPosts->count() > 0)
<section class="py-lg-10 py-6">
    <div class="container">
        <div class="row mb-md-8 mb-4">
            <div class="col-lg-12">
                <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-md-between gap-4">
                    <!--Heading-->
                    <div class="col-sm-7">
                        <h2 class="mb-0">Our Blog Posts</h2>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('frontend.blog.index') }}" class="d-flex align-items-center gap-2 btn-dark-link">
                            <span class="text-link">All Posts</span>
                            <span class="btn btn-outline-primary btn-icon btn-xxs rounded-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor"
                                    class="bi bi-chevron-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-4">
            @foreach($randomPosts as $post)
                <div class="col-lg-4 col-md-6">
                    <article class="">
                        <a href="{{ $post->url }}" class="position-relative d-flex">
                            <figure class="img-hover mb-0 w-100" style="height: 250px; overflow: hidden;">
                                @if($post->cover_image)
                                    <img src="{{ asset('storage/' . $post->cover_image) }}" 
                                         alt="{{ $post->title }}" 
                                         class="img-fluid w-100 h-100" 
                                         style="object-fit: cover; object-position: center;" />
                                @else
                                    <img src="{{ frontend_asset('images/blog/blog-img-1.jpg') }}" 
                                         alt="{{ $post->title }}" 
                                         class="img-fluid w-100 h-100" 
                                         style="object-fit: cover; object-position: center;" />
                                @endif
                            </figure>
                            @if($post->category)
                                <div class="position-absolute bottom-0 p-3">
                                    <span class="badge text-bg-info">{{ $post->category }}</span>
                                </div>
                            @endif
                        </a>
                        <div class="mt-4">
                            <h3 class="fs-5">
                                <a href="{{ $post->url }}" class="text-inherit">{{ $post->title }}</a>
                            </h3>
                            @if($post->short_description)
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($post->short_description), 120) }}</p>
                            @elseif($post->content)
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}</p>
                            @endif
                            <p class="d-flex gap-3 align-items-center">
                                <span class="d-flex align-items-center gap-1 small">
                                    <svg width="16" height="17" viewBox="0 0 16 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.2"
                                            d="M13.5 3.5V6H2.5V3.5C2.5 3.36739 2.55268 3.24021 2.64645 3.14645C2.74021 3.05268 2.86739 3 3 3H13C13.1326 3 13.2598 3.05268 13.3536 3.14645C13.4473 3.24021 13.5 3.36739 13.5 3.5Z"
                                            fill="#4E4E4E" />
                                        <path
                                            d="M13 2.5H11.5V2C11.5 1.86739 11.4473 1.74021 11.3536 1.64645C11.2598 1.55268 11.1326 1.5 11 1.5C10.8674 1.5 10.7402 1.55268 10.6464 1.64645C10.5527 1.74021 10.5 1.86739 10.5 2V2.5H5.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V2.5H3C2.73478 2.5 2.48043 2.60536 2.29289 2.79289C2.10536 2.98043 2 3.23478 2 3.5V13.5C2 13.7652 2.10536 14.0196 2.29289 14.2071C2.48043 14.3946 2.73478 14.5 3 14.5H13C13.2652 14.5 13.5196 14.3946 13.7071 14.2071C13.8946 14.0196 14 13.7652 14 13.5V3.5C14 3.23478 13.8946 2.98043 13.7071 2.79289C13.5196 2.60536 13.2652 2.5 13 2.5ZM4.5 3.5V4C4.5 4.13261 4.55268 4.25979 4.64645 4.35355C4.74021 4.44732 4.86739 4.5 5 4.5C5.13261 4.5 5.25979 4.44732 5.35355 4.35355C5.44732 4.25979 5.5 4.13261 5.5 4V3.5H10.5V4C10.5 4.13261 10.5527 4.25979 10.6464 4.35355C10.7402 4.44732 10.8674 4.5 11 4.5C11.1326 4.5 11.2598 4.44732 11.3536 4.35355C11.4473 4.25979 11.5 4.13261 11.5 4V3.5H13V5.5H3V3.5H4.5ZM13 13.5H3V6.5H13V13.5Z"
                                            fill="#211F1C" />
                                    </svg>
                                    {{ $post->created_at->format('d M, Y') }}
                                </span>
                            </p>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
<!--Blog Section end-->

@endsection

