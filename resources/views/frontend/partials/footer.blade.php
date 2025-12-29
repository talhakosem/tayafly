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

<footer class="pt-lg-8 pt-6 pb-4 bg-light border-top">
    <div class="container">
        <div class="row gy-4 pb-3">
            <!-- Site Info & Social -->
            <div class="col-lg-4 col-md-6">
                <div class="mb-4">
                    @if($settings->logo)
                        <img src="{{ asset('storage/' . $settings->logo) }}" alt="{{ $settings->site_title }}" class="mb-3" style="max-height: 40px;">
                    @else
                        <h4 class="mb-3">{{ $settings->site_title ?? config('app.name') }}</h4>
                    @endif
                    <p class="text-muted small mb-4">
                        {{ $settings->site_description }}
                    </p>
                    <div class="d-flex gap-2">
                        @if($settings->facebook_url)
                            <a href="{{ $settings->facebook_url }}" target="_blank" class="footer-social-link"><i class="bi bi-facebook"></i></a>
                        @endif
                        @if($settings->instagram_url)
                            <a href="{{ $settings->instagram_url }}" target="_blank" class="footer-social-link"><i class="bi bi-instagram"></i></a>
                        @endif
                        @if($settings->twitter_url)
                            <a href="{{ $settings->twitter_url }}" target="_blank" class="footer-social-link"><i class="bi bi-twitter-x"></i></a>
                        @endif
                        @if($settings->youtube_url)
                            <a href="{{ $settings->youtube_url }}" target="_blank" class="footer-social-link"><i class="bi bi-youtube"></i></a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-4 col-md-6">
                <h6 class="mb-3 fw-bold">İletişim Bilgileri</h6>
                <ul class="list-unstyled mb-0 small">
                    @if($settings->phone)
                        <li class="mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-telephone text-primary"></i>
                            <a href="tel:{{ $settings->phone }}" class="text-decoration-none text-muted">{{ $settings->phone }}</a>
                        </li>
                    @endif
                    @if($settings->email)
                        <li class="mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-envelope text-primary"></i>
                            <a href="mailto:{{ $settings->email }}" class="text-decoration-none text-muted">{{ $settings->email }}</a>
                        </li>
                    @endif
                    @if($settings->whatsapp)
                        <li class="mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-whatsapp text-success"></i>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->whatsapp) }}" target="_blank" class="text-decoration-none text-muted">WhatsApp</a>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Address Info -->
            <div class="col-lg-4 col-md-12 ms-lg-auto">
                <h6 class="mb-3 fw-bold">Adres</h6>
                <ul class="list-unstyled mb-0 small">
                    @if($settings->address)
                        <li class="mb-3 d-flex align-items-start gap-2">
                            <i class="bi bi-geo-alt text-primary"></i>
                            <div class="text-muted">{!! $settings->address !!}</div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="row border-top pt-3">
            <div class="col-12">
                <div class="text-center">
                    <p class="mb-0 text-muted small">
                        Copyrights ©{{ date('Y') }} TayaFly. <br>Designed & Developed by <a href="https://tayadm.com" target="_blank" class="text-decoration-none text-muted">Taya Digital Media</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
