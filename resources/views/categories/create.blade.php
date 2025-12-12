@extends('layouts.admin')

@section('title', 'Yeni Kategori')

@section('content')
<div class="row mb-8">
    <div class="col-md-12">
        <div>
            <h2>Yeni Kategori Oluştur</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-inherit">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}" class="text-inherit">Kategoriler</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Yeni Kategori</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data" class="row g-6">
    @csrf
    
    <div class="col-lg-8 col-12">
        <div class="card card-lg">
            <div class="card-body p-6 d-flex flex-column gap-4">
                <!-- Kategori Adı -->
                <div>
                    <label for="name" class="form-label">Kategori Adı *</label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           placeholder="Kategori adı" 
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
                           value="{{ old('slug') }}"
                           placeholder="kategori-adi (otomatik oluşturulur)">
                    <small class="form-text text-muted">Boş bırakılırsa kategori adından otomatik oluşturulur</small>
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
                              placeholder="Kategori açıklaması">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Görsel -->
                <div>
                    <label class="form-label">Görsel</label>
                    <input type="file" 
                           class="form-control @error('image') is-invalid @enderror" 
                           id="image" 
                           name="image"
                           accept="image/*">
                    <small class="form-text text-muted">Maksimum 2MB - JPG, PNG, GIF formatları</small>
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
                           value="{{ old('sort_order', 0) }}"
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
                           value="{{ old('meta_title') }}"
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
                              placeholder="Meta açıklama">{{ old('meta_description') }}</textarea>
                    @error('meta_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Üst Menüde Göster -->
                <div>
                    <div class="form-check form-switch ps-0">
                        <label class="form-check-label" for="show_in_top_menu">Üst Menüde Göster</label>
                        <input class="form-check-input ms-auto" 
                               type="checkbox" 
                               role="switch" 
                               id="show_in_top_menu" 
                               name="show_in_top_menu"
                               value="1"
                               {{ old('show_in_top_menu') ? 'checked' : '' }}>
                    </div>
                </div>

                <!-- Buttons -->
                <div>
                    <div class="d-flex flex-row gap-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-check-circle me-2"></i>Kaydet
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-light w-100">
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
    // Kategori adı değiştiğinde slug'ı otomatik oluştur
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('input', function() {
        if (!slugInput.value || slugInput.dataset.manual !== 'true') {
            const slug = this.value
                .toLowerCase()
                .replace(/ğ/g, 'g')
                .replace(/ü/g, 'u')
                .replace(/ş/g, 's')
                .replace(/ı/g, 'i')
                .replace(/ö/g, 'o')
                .replace(/ç/g, 'c')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        }
    });
    
    // Slug manuel değiştirildiğinde işaretle
    slugInput.addEventListener('input', function() {
        this.dataset.manual = 'true';
    });
});
</script>
@endpush

