@extends('layouts.admin')

@section('title', 'Bloglar')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Blog Yazıları</h2>
            <a href="{{ route('posts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Yeni Yazı
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">Görsel</th>
                                <th>Başlık</th>
                                <th>Kategori</th>
                                <th>Durum</th>
                                <th>Tarih</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                                <tr>
                                    <td>
                                        @if($post->cover_image)
                                            <img src="{{ asset('storage/' . $post->cover_image) }}" 
                                                 alt="{{ $post->title }}" 
                                                 class="rounded"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $post->title }}</div>
                                        <small class="text-muted">{{ $post->slug }}</small>
                                    </td>
                                    <td>
                                        @if($post->categories->first())
                                            <span class="badge bg-info">{{ $post->categories->first()->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($post->is_published)
                                            <span class="badge bg-success">Yayında</span>
                                        @else
                                            <span class="badge bg-warning">Taslak</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $post->created_at->format('d.m.Y') }}</small><br>
                                        <small class="text-muted">{{ $post->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('posts.edit', $post) }}" 
                                           class="btn btn-sm btn-outline-secondary me-2"
                                           title="Düzenle">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger delete-post-btn" 
                                                data-post-id="{{ $post->id }}"
                                                title="Sil">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        
                                        <form id="delete-form-{{ $post->id }}" action="{{ route('posts.destroy', $post) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox display-4 text-muted"></i>
                                        <p class="text-muted mb-0 mt-2">Henüz yazı yok.</p>
                                        <a href="{{ route('posts.create') }}" class="btn btn-sm btn-primary mt-2">
                                            İlk Yazıyı Oluştur
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($posts->hasPages())
        <div class="col-12 mt-4">
            <div>
                <nav class="mt-7 mt-lg-10">
                    <ul class="pagination mb-0">
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
                                <li class="page-item"><a class="page-link active" href="#!">{{ $page }}</a></li>
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
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-post-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            if (confirm('Bu yazıyı silmek istediğinize emin misiniz?')) {
                document.getElementById('delete-form-' + postId).submit();
            }
        });
    });
});
</script>
@endpush