@extends('layouts.admin')

@section('title', 'Kategoriler')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Kategoriler</h2>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Yeni Kategori
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">Görsel</th>
                                <th>Kategori Adı</th>
                                <th>Sıra</th>
                                <th>Tarih</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>
                                        @if($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}" 
                                                 alt="{{ $category->name }}" 
                                                 class="rounded"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-folder text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $category->name }}</div>
                                        <small class="text-muted">{{ $category->slug }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $category->sort_order }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $category->created_at->format('d.m.Y') }}</small><br>
                                        <small class="text-muted">{{ $category->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('categories.edit', $category) }}" 
                                           class="btn btn-sm btn-outline-secondary me-2"
                                           title="Düzenle">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger delete-category-btn" 
                                                data-category-id="{{ $category->id }}"
                                                title="Sil">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        
                                        <form id="delete-form-{{ $category->id }}" action="{{ route('categories.destroy', $category) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-inbox display-4 text-muted"></i>
                                        <p class="text-muted mb-0 mt-2">Henüz kategori yok.</p>
                                        <a href="{{ route('categories.create') }}" class="btn btn-sm btn-primary mt-2">
                                            İlk Kategoriyi Oluştur
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($categories->hasPages())
        <div class="col-12 mt-4">
            <div>
                <nav class="mt-7 mt-lg-10">
                    <ul class="pagination mb-0">
                        {{-- Previous Page Link --}}
                        @if ($categories->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $categories->previousPageUrl() }}">Previous</a></li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $currentPage = $categories->currentPage();
                            $lastPage = $categories->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp

                        @if($startPage > 1)
                            <li class="page-item"><a class="page-link" href="{{ $categories->url(1) }}">1</a></li>
                            @if($startPage > 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif

                        @for ($page = $startPage; $page <= $endPage; $page++)
                            @if ($page == $currentPage)
                                <li class="page-item"><a class="page-link active" href="#!">{{ $page }}</a></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $categories->url($page) }}">{{ $page }}</a></li>
                            @endif
                        @endfor

                        @if($endPage < $lastPage)
                            @if($endPage < $lastPage - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item"><a class="page-link" href="{{ $categories->url($lastPage) }}">{{ $lastPage }}</a></li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($categories->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $categories->nextPageUrl() }}">Next</a></li>
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
    document.querySelectorAll('.delete-category-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category-id');
            if (confirm('Bu kategoriyi silmek istediğinize emin misiniz?')) {
                document.getElementById('delete-form-' + categoryId).submit();
            }
        });
    });
});
</script>
@endpush

