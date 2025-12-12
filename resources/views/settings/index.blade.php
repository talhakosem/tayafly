@extends('layouts.admin')

@section('title', 'Site Ayarları')

@push('styles')
<link href="{{ admin_asset('libs/dropzone/dist/min/dropzone.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="row mb-8">
    <div class="col-md-12">
        <div>
            <h2>Site Ayarları</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-inherit">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Site Ayarları</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                Genel Bilgiler
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                İletişim
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab">
                Sosyal Medya
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                SEO & Analytics
            </button>
        </li>
    </ul>

    <div class="tab-content" id="settingsTabsContent">
        <!-- Genel Bilgiler Tab -->
        <div class="tab-pane fade show active" id="general" role="tabpanel">
            <div class="card card-lg">
                <div class="card-body p-6">
                    <h5 class="mb-4">Genel Bilgiler</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="site_title" class="form-label">Site Başlığı *</label>
                            <input type="text" class="form-control @error('site_title') is-invalid @enderror" 
                                   id="site_title" name="site_title" 
                                   value="{{ old('site_title', $setting->site_title) }}" required>
                            @error('site_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="top_link" class="form-label">Üst Link</label>
                            <input type="text" class="form-control @error('top_link') is-invalid @enderror" 
                                   id="top_link" name="top_link" 
                                   value="{{ old('top_link', $setting->top_link) }}">
                            @error('top_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="site_description" class="form-label">Site Açıklaması *</label>
                            <textarea class="form-control @error('site_description') is-invalid @enderror" 
                                      id="site_description" name="site_description" rows="3" required>{{ old('site_description', $setting->site_description) }}</textarea>
                            @error('site_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="logo" class="form-label">Logo</label>
                            @if($setting->logo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $setting->logo) }}" alt="Logo" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                   id="logo" name="logo" accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="favicon" class="form-label">Favicon</label>
                            @if($setting->favicon)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $setting->favicon) }}" alt="Favicon" class="img-thumbnail" style="max-width: 50px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('favicon') is-invalid @enderror" 
                                   id="favicon" name="favicon" accept="image/*">
                            @error('favicon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- İletişim Tab -->
        <div class="tab-pane fade" id="contact" role="tabpanel">
            <div class="card card-lg">
                <div class="card-body p-6">
                    <h5 class="mb-4">İletişim Bilgileri</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="email" class="form-label">E-posta *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" 
                                   value="{{ old('email', $setting->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Telefon *</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" 
                                   value="{{ old('phone', $setting->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="whatsapp" class="form-label">WhatsApp</label>
                            <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" 
                                   id="whatsapp" name="whatsapp" 
                                   value="{{ old('whatsapp', $setting->whatsapp) }}">
                            @error('whatsapp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label">Adres *</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="4" required>{{ old('address', $setting->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="google_map" class="form-label">Google Harita Kodu</label>
                            <textarea class="form-control @error('google_map') is-invalid @enderror" 
                                      id="google_map" name="google_map" rows="3">{{ old('google_map', $setting->google_map) }}</textarea>
                            @error('google_map')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sosyal Medya Tab -->
        <div class="tab-pane fade" id="social" role="tabpanel">
            <div class="card card-lg">
                <div class="card-body p-6">
                    <h5 class="mb-4">Sosyal Medya Linkleri</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="facebook_url" class="form-label">Facebook URL</label>
                            <input type="url" class="form-control @error('facebook_url') is-invalid @enderror" 
                                   id="facebook_url" name="facebook_url" 
                                   value="{{ old('facebook_url', $setting->facebook_url) }}">
                            @error('facebook_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="instagram_url" class="form-label">Instagram URL</label>
                            <input type="url" class="form-control @error('instagram_url') is-invalid @enderror" 
                                   id="instagram_url" name="instagram_url" 
                                   value="{{ old('instagram_url', $setting->instagram_url) }}">
                            @error('instagram_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="twitter_url" class="form-label">Twitter URL</label>
                            <input type="url" class="form-control @error('twitter_url') is-invalid @enderror" 
                                   id="twitter_url" name="twitter_url" 
                                   value="{{ old('twitter_url', $setting->twitter_url) }}">
                            @error('twitter_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="youtube_url" class="form-label">YouTube URL</label>
                            <input type="url" class="form-control @error('youtube_url') is-invalid @enderror" 
                                   id="youtube_url" name="youtube_url" 
                                   value="{{ old('youtube_url', $setting->youtube_url) }}">
                            @error('youtube_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO & Analytics Tab -->
        <div class="tab-pane fade" id="seo" role="tabpanel">
            <div class="card card-lg">
                <div class="card-body p-6">
                    <h5 class="mb-4">SEO & Analytics</h5>
                    <div class="row g-4">
                        <div class="col-12">
                            <label for="google_verification_code" class="form-label">Google Doğrulama Kodu</label>
                            <input type="text" class="form-control @error('google_verification_code') is-invalid @enderror" 
                                   id="google_verification_code" name="google_verification_code" 
                                   value="{{ old('google_verification_code', $setting->google_verification_code) }}">
                            @error('google_verification_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="analytics_code" class="form-label">Analytics Kodu</label>
                            <textarea class="form-control @error('analytics_code') is-invalid @enderror" 
                                      id="analytics_code" name="analytics_code" rows="6">{{ old('analytics_code', $setting->analytics_code) }}</textarea>
                            @error('analytics_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Submit Button -->
    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-2"></i>Ayarları Kaydet
        </button>
    </div>
</form>
@endsection

