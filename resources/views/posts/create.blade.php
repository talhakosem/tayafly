@extends('layouts.admin')

@section('title', 'Yeni Blog Yazısı')

@push('styles')
<link href="{{ admin_asset('libs/dropzone/dist/min/dropzone.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ admin_asset('libs/@yaireo/tagify/dist/tagify.css') }}" />
<link href="{{ admin_asset('libs/quill/dist/quill.snow.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="row mb-8">
    <div class="col-md-12">
        <div>
            <h2>Yeni Blog Yazısı Oluştur</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-inherit">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('posts.index') }}" class="text-inherit">Blog</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Yeni Yazı</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="row g-6">
    @csrf
    
    <div class="col-lg-8 col-12">
        <div class="card card-lg">
            <div class="card-body p-6 d-flex flex-column gap-4">
                <!-- Başlık -->
                <div>
                    <label for="title" class="form-label">Başlık *</label>
                    <input type="text" 
                           class="form-control @error('title') is-invalid @enderror" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}"
                           placeholder="Yazı başlığı" 
                           required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Kapak Görseli -->
                <div>
                    <label class="form-label">Kapak Görseli</label>
                    <input type="file" 
                           class="form-control @error('cover_image') is-invalid @enderror" 
                           id="cover_image" 
                           name="cover_image"
                           accept="image/*">
                    <small class="form-text text-muted">Maksimum 2MB - JPG, PNG, GIF formatları</small>
                    @error('cover_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Kısa Açıklama -->
                <div>
                    <label for="short_description" class="form-label">Kısa Açıklama</label>
                    <textarea class="form-control @error('short_description') is-invalid @enderror" 
                              id="short_description" 
                              name="short_description" 
                              rows="3"
                              placeholder="Kısa bir açıklama yazın">{{ old('short_description') }}</textarea>
                    @error('short_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- İçerik -->
                <div>
                    <label class="form-label">İçerik *</label>
                    <div id="editor" style="height: 300px;">{!! old('content') !!}</div>
                    <textarea name="content" id="content" class="d-none @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-12">
        <div class="card card-lg">
            <div class="card-body p-6 d-flex flex-column gap-4">
                <!-- Kategori -->
                <div>
                    <label for="category_id" class="form-label">Kategori</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" 
                            id="category_id" 
                            name="category_id">
                        <option value="">Kategori Seçin</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tags -->
                <div>
                    <label class="form-label" for="tags">Etiketler</label>
                    <input type="text" 
                           class="form-control @error('tags') is-invalid @enderror" 
                           id="tags" 
                           name="tags"
                           value="{{ old('tags') }}"
                           placeholder="Virgülle ayırın: etiket1, etiket2">
                    <small class="form-text text-muted">Örnek: market, alışveriş, organik</small>
                    @error('tags')
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

                <!-- Yayınla -->
                <div>
                    <div class="form-check form-switch ps-0">
                        <label class="form-check-label" for="is_published">Yayınla</label>
                        <input class="form-check-input ms-auto" 
                               type="checkbox" 
                               role="switch" 
                               id="is_published" 
                               name="is_published"
                               value="1"
                               {{ old('is_published', true) ? 'checked' : '' }}>
                    </div>
                </div>

                <!-- Buttons -->
                <div>
                    <div class="d-flex flex-row gap-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-check-circle me-2"></i>Kaydet
                        </button>
                        <a href="{{ route('posts.index') }}" class="btn btn-light w-100">
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
<script src="{{ admin_asset('libs/quill/dist/quill.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quill Editor
    var quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'align': [] }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    // Quill içeriği değiştiğinde textarea'ya aktar
    quill.on('text-change', function() {
        const contentTextarea = document.querySelector('#content');
        if (contentTextarea) {
            contentTextarea.value = quill.root.innerHTML;
        }
    });

    // Form submit olduğunda da Quill içeriğini textarea'ya aktar
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const contentTextarea = document.querySelector('#content');
            if (contentTextarea) {
                contentTextarea.value = quill.root.innerHTML;
            }
        });
    }
});
</script>
@endpush
