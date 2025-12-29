@extends('layouts.admin')

@section('title', 'Blog Yazısını Düzenle')

@push('styles')
<link href="{{ admin_asset('libs/dropzone/dist/min/dropzone.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ admin_asset('libs/@yaireo/tagify/dist/tagify.css') }}" />
<link href="{{ admin_asset('libs/quill/dist/quill.snow.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="row mb-8">
    <div class="col-md-12">
        <div>
            <h2>Blog Yazısını Düzenle</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-inherit">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('posts.index') }}" class="text-inherit">Blog</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Düzenle</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Doğrulama Hataları {{ Auth::user()->name }}</h5>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="row g-6">
    @csrf
    @method('PUT')
    
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
                           value="{{ old('title', $post->title) }}"
                           placeholder="Yazı başlığı" 
                           required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Slug: {{ $post->slug }}</small>
                </div>

                <!-- Kapak Görseli -->
                <div>
                    <label class="form-label">Kapak Görseli</label>
                    @if($post->cover_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $post->cover_image) }}" 
                                 alt="Mevcut Görsel" 
                                 class="img-thumbnail" 
                                 style="max-width: 200px;">
                        </div>
                    @endif
                    <input type="file" 
                           class="form-control @error('cover_image') is-invalid @enderror" 
                           id="cover_image" 
                           name="cover_image"
                           accept="image/*"
                           onchange="checkFileSize(this)">
                    <small class="form-text text-muted">
                        Maksimum 5MB - JPG, PNG, GIF, WEBP formatları
                        @php
                            $maxUpload = ini_get('upload_max_filesize');
                            $maxPost = ini_get('post_max_size');
                        @endphp
                        (Sunucu limiti: {{ $maxUpload }})
                    </small>
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
                              placeholder="Kısa bir açıklama yazın">{{ old('short_description', $post->short_description) }}</textarea>
                    @error('short_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- İçerik -->
                <div>
                    <label class="form-label">İçerik *</label>
                    <div id="editor" style="height: 300px;">{!! old('content', $post->content) !!}</div>
                    <textarea name="content" id="content" class="d-none @error('content') is-invalid @enderror" required>{{ old('content', $post->content) }}</textarea>
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
                    <label for="category_id" class="form-label">Kategori *</label>
                    @php
                        $selectedCategoryId = old('category_id', $post->categories->first()?->id);
                    @endphp
                    <select class="form-select @error('category_id') is-invalid @enderror" 
                            id="category_id" 
                            name="category_id"
                            required>
                        <option value="">Kategori Seçin</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Destinasyonlar -->
                <div>
                    <label class="form-label">Destinasyonlar</label>
                    @php
                        $selectedDestinationIds = old('destination_ids', $post->destinations->pluck('id')->toArray());
                    @endphp
                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        @forelse($destinations as $destination)
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="destination_ids[]" 
                                       value="{{ $destination->id }}" 
                                       id="destination_{{ $destination->id }}"
                                       {{ in_array($destination->id, $selectedDestinationIds) ? 'checked' : '' }}>
                                <label class="form-check-label" for="destination_{{ $destination->id }}">
                                    {{ $destination->name }}
                                </label>
                            </div>
                        @empty
                            <p class="text-muted mb-0 small">Henüz destinasyon eklenmemiş.</p>
                        @endforelse
                    </div>
                    <small class="form-text text-muted">Birden fazla destinasyon seçebilirsiniz</small>
                    @error('destination_ids')
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
                           value="{{ old('tags', is_array($post->tags) ? implode(', ', $post->tags) : '') }}"
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
                           value="{{ old('meta_title', $post->meta_title) }}"
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
                              placeholder="Meta açıklama">{{ old('meta_description', $post->meta_description) }}</textarea>
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
                               {{ old('is_published', $post->is_published) ? 'checked' : '' }}>
                    </div>
                </div>

                <!-- Buttons -->
                <div>
                    <div class="d-flex flex-row gap-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-check-circle me-2"></i>Güncelle
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
function checkFileSize(input) {
    if (input.files && input.files[0]) {
        const fileSize = input.files[0].size / 1024 / 1024; // MB
        const maxSize = 5; // MB
        
        if (fileSize > maxSize) {
            alert('Dosya boyutu ' + maxSize + 'MB\'dan büyük olamaz. Seçilen dosya: ' + fileSize.toFixed(2) + 'MB');
            input.value = '';
            return false;
        }
        
        // Dosya adını göster
        const fileName = input.files[0].name;
        const fileInfo = document.createElement('small');
        fileInfo.className = 'd-block mt-2 text-success';
        fileInfo.textContent = 'Seçilen: ' + fileName + ' (' + fileSize.toFixed(2) + 'MB)';
        
        // Eski bilgiyi kaldır
        const oldInfo = input.parentElement.querySelector('.text-success');
        if (oldInfo) oldInfo.remove();
        
        input.parentElement.appendChild(fileInfo);
    }
}

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

    // Form submit olduğunda Quill içeriğini textarea'ya aktar
    document.querySelector('form').addEventListener('submit', function(e) {
        const content = quill.root.innerHTML;
        document.querySelector('#content').value = content;
        
        // Form validation
        const title = document.querySelector('#title').value.trim();
        if (!title) {
            alert('Başlık alanı zorunludur!');
            e.preventDefault();
            return false;
        }
        
        const categoryId = document.querySelector('#category_id').value;
        if (!categoryId) {
            alert('Önce kategori seçmelisin!');
            e.preventDefault();
            return false;
        }
        
        if (!content || content === '<p><br></p>') {
            alert('İçerik alanı zorunludur!');
            e.preventDefault();
            return false;
        }
        
        console.log('Form gönderiliyor...', {
            title: title,
            contentLength: content.length,
            hasFile: document.querySelector('#cover_image').files.length > 0
        });
    });
});
</script>
@endpush
