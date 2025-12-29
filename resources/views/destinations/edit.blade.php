@extends('layouts.admin')

@section('title', 'Destinasyon Düzenle')

@section('content')
<div class="row mb-8">
    <div class="col-md-12">
        <div>
            <h2>Destinasyon Düzenle</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-inherit">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('destinations.index') }}" class="text-inherit">Destinasyonlar</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Düzenle</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<form action="{{ route('destinations.update', $destination) }}" method="POST" enctype="multipart/form-data" class="row g-6">
    @csrf
    @method('PUT')
    
    <div class="col-lg-8 col-12">
        <div class="card card-lg">
            <div class="card-body p-6 d-flex flex-column gap-4">
                <!-- Destinasyon Adı -->
                <div>
                    <label for="name" class="form-label">Destinasyon Adı *</label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $destination->name) }}"
                           placeholder="Destinasyon adı" 
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" 
                           class="form-control @error('slug') is-invalid @enderror" 
                           id="slug" 
                           name="slug" 
                           value="{{ old('slug', $destination->slug) }}"
                           placeholder="destinasyon-adi">
                    <small class="form-text text-muted">Mevcut slug: {{ $destination->slug }}</small>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Açıklama -->
                <div>
                    <label for="description" class="form-label">Açıklama</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="5"
                              placeholder="Destinasyon hakkında kısa açıklama">{{ old('description', $destination->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Görsel -->
                <div>
                    <label class="form-label">Görsel</label>
                    @if($destination->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $destination->image) }}" 
                                 alt="Mevcut Görsel" 
                                 class="img-thumbnail" 
                                 style="max-width: 200px;">
                        </div>
                    @endif
                    <input type="file" 
                           class="form-control @error('image') is-invalid @enderror" 
                           id="image" 
                           name="image"
                           accept="image/*">
                    <small class="form-text text-muted">Maksimum 2MB - JPG, PNG, GIF, WEBP formatları</small>
                    @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-12">
        <div class="card card-lg">
            <div class="card-body p-6 d-flex flex-column gap-4">
                <!-- Sıra -->
                <div>
                    <label for="sort_order" class="form-label">Sıra</label>
                    <input type="number" 
                           class="form-control @error('sort_order') is-invalid @enderror" 
                           id="sort_order" 
                           name="sort_order"
                           value="{{ old('sort_order', $destination->sort_order) }}"
                           min="0">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- SEO Başlık -->
                <div>
                    <label for="meta_title" class="form-label">SEO Başlık</label>
                    <input type="text" 
                           class="form-control @error('meta_title') is-invalid @enderror" 
                           id="meta_title" 
                           name="meta_title"
                           value="{{ old('meta_title', $destination->meta_title) }}"
                           placeholder="Meta başlık">
                    @error('meta_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- SEO Açıklama -->
                <div>
                    <label for="meta_description" class="form-label">SEO Açıklama</label>
                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                              id="meta_description" 
                              name="meta_description" 
                              rows="3"
                              placeholder="Meta açıklama">{{ old('meta_description', $destination->meta_description) }}</textarea>
                    @error('meta_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Aktif -->
                <div>
                    <div class="form-check form-switch ps-0">
                        <label class="form-check-label" for="is_active">Aktif</label>
                        <input class="form-check-input ms-auto" 
                               type="checkbox" 
                               role="switch" 
                               id="is_active" 
                               name="is_active"
                               value="1"
                               {{ old('is_active', $destination->is_active) ? 'checked' : '' }}>
                    </div>
                </div>

                <!-- Footer'da Göster -->
                <div>
                    <div class="form-check form-switch ps-0">
                        <label class="form-check-label" for="show_in_menu">Footer'da Göster</label>
                        <input class="form-check-input ms-auto" 
                               type="checkbox" 
                               role="switch" 
                               id="show_in_menu" 
                               name="show_in_menu"
                               value="1"
                               {{ old('show_in_menu', $destination->show_in_menu) ? 'checked' : '' }}>
                    </div>
                </div>

                <!-- Blog Sayısı Bilgisi -->
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Bu destinasyona ait <strong>{{ $destination->posts()->count() }}</strong> blog yazısı var.
                </div>

                <!-- Buttons -->
                <div>
                    <div class="d-flex flex-row gap-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-check-circle me-2"></i>Güncelle
                        </button>
                        <a href="{{ route('destinations.index') }}" class="btn btn-light w-100">
                            <i class="bi bi-x-circle me-2"></i>İptal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Slug değiştirildiğinde işaretle
    const slugInput = document.getElementById('slug');
    slugInput.dataset.manual = 'true';
});
</script>
@endpush

