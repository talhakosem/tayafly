@extends('layouts.frontend')

@section('title', 'Private Jet Charter ' . $destination->name)

@section('meta_description', $destination->meta_description ?? 'Private Jet Charter ' . $destination->name)

@push('styles')
<style>
    .blog-list-img {
        width: 100%;
        height: 330px;
        object-fit: cover;
        object-position: center;
        border-radius: 12px;
    }
</style>
@endpush

@section('content')
<!--Pageheader start-->
<section class="bg-light bg-opacity-50 py-lg-10 py-6">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center">
                    <h1 class="display-5 fw-bold">Private Jet Charter {{ $destination->name }}</h1>
                    @if($destination->description)
                        <p class="mb-0">{{ $destination->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<!--Pageheader end-->

<!--Blog posts start-->
<div class="py-6 py-lg-10">
    <div class="container">
        <div class="row gy-4">
            @forelse($posts as $post)
                <!--Blog post-->
                <div class="col-lg-4 col-md-6">
                    <article>
                        <a href="{{ url('/' . ($post->categories->first()->slug ?? '') . '/' . $post->slug) }}" class="position-relative d-flex">
                            <figure class="img-hover mb-0">
                                @if($post->cover_image)
                                    <img src="{{ asset('storage/' . $post->cover_image) }}" 
                                         alt="{{ $post->title }}" 
                                         class="img-fluid blog-list-img" />
                                @else
                                    <img src="{{ frontend_asset('images/blog/blog-img-1.jpg') }}" 
                                         alt="{{ $post->title }}" 
                                         class="img-fluid blog-list-img" />
                                @endif
                            </figure>
                            @if($post->categories->first())
                                <div class="position-absolute bottom-0 p-3">
                                    <span class="badge text-bg-info">{{ $post->categories->first()->name }}</span>
                                </div>
                            @endif
                        </a>
                        <div class="mt-4">
                            <h3 class="fs-5">
                                <a href="{{ url('/' . ($post->categories->first()->slug ?? '') . '/' . $post->slug) }}" class="text-inherit">
                                    {{ html_entity_decode($post->title, ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                                </a>
                            </h3>
                            <p>
                                {{ $post->short_description ? html_entity_decode($post->short_description, ENT_QUOTES | ENT_HTML5, 'UTF-8') : \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($post->content), ENT_QUOTES | ENT_HTML5, 'UTF-8'), 150) }}
                            </p>
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
                                    {{ $post->created_at->format('d M Y') }}
                                </span>
                            </p>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <p class="text-muted">Bu destinasyon için henüz blog yazısı bulunmuyor.</p>
                    </div>
                </div>
            @endforelse

            @if($posts->hasPages())
                <div class="col-12 text-center mt-6">
                    <nav>
                        <ul class="pagination justify-content-center mb-0">
                            {{-- Previous Page Link --}}
                            @if ($posts->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">Previous</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $posts->previousPageUrl() }}">Previous</a></li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $posts->currentPage();
                                $lastPage = $posts->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);
                            @endphp

                            @if($startPage > 1)
                                <li class="page-item"><a class="page-link" href="{{ $posts->url(1) }}">1</a></li>
                                @if($startPage > 2)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                            @endif

                            @for ($page = $startPage; $page <= $endPage; $page++)
                                @if ($page == $currentPage)
                                    <li class="page-item active"><a class="page-link" href="#!">{{ $page }}</a></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $posts->url($page) }}">{{ $page }}</a></li>
                                @endif
                            @endfor

                            @if($endPage < $lastPage)
                                @if($endPage < $lastPage - 1)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                                <li class="page-item"><a class="page-link" href="{{ $posts->url($lastPage) }}">{{ $lastPage }}</a></li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($posts->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $posts->nextPageUrl() }}">Next</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">Next</span></li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>
<!--Blog posts end-->
@endsection

