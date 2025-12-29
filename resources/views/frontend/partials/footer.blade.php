@php
    $footerDestinations = \App\Models\Destination::where('is_active', true)
        ->where('show_in_menu', true)
        ->orderBy('sort_order')
        ->get();
    
    $settings = \App\Models\Setting::getSettings();
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
        font-size: 0.875rem;
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
    .footer-social-link {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: #f8f9fa;
        color: #6c757d;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    .footer-social-link:hover {
        background-color: #0d6efd;
        color: #fff;
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

<footer class="bg-light border-top py-4">
    <div class="container">
        <div class="row align-items-center">
            <!-- Adres -->
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                @if($settings->address)
                    <div class="d-inline-flex align-items-start gap-2 text-start">
                        <i class="bi bi-geo-alt text-primary flex-shrink-0" style="font-size: 1rem; position: relative; top: 0px;"></i>
                        <div class="text-muted small" style="line-height: 1.6;">{!! $settings->address !!}</div>
                    </div>
                @endif
            </div>

            <!-- Copyright -->
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 text-muted small">
                    Copyrights ©{{ date('Y') }} TayaFly. 
                    Designed & Developed by <a href="https://tayadm.com" target="_blank" class="text-decoration-none text-muted">Taya Digital Media</a>
                </p>
            </div>
        </div>
    </div>
</footer>
