@php
    $footerDestinations = \App\Models\Destination::where('is_active', true)
        ->where('show_in_menu', true)
        ->orderBy('sort_order')
        ->get();
@endphp

<!-- Destinations Section -->
@if($footerDestinations->count() > 0)
<style>
    .destinations-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem 2rem;
    }
    .destinations-grid a {
        white-space: nowrap;
        text-align: left;
    }
    @media (max-width: 991px) {
        .destinations-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 575px) {
        .destinations-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
<div class="bg-white border-top py-5">
    <div class="container">
        <div class="destinations-grid">
            @foreach($footerDestinations as $destination)
                <a href="{{ route('destination.show', $destination->slug) }}" 
                   class="text-decoration-none text-primary">
                    Private Jet Charter {{ $destination->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<footer class="pt-lg-6 pt-4 pb-4 bg-light border-top">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="text-center">
                    <p class="mb-0 text-muted">
                        © {{ date('Y') }} {{ $settings->site_title ?? config('app.name') }}. Made by <a href="https://tayadm.com" target="_blank" class="text-decoration-none">TayaDM</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
