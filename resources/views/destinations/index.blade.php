@extends('layouts.admin')

@section('title', 'Destinasyonlar')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Destinasyonlar</h2>
            <a href="{{ route('destinations.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Yeni Destinasyon
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">Görsel</th>
                                <th>Destinasyon Adı</th>
                                <th>Durum</th>
                                <th>Sıra</th>
                                <th>Blog Sayısı</th>
                                <th>Tarih</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($destinations as $destination)
                                <tr>
                                    <td>
                                        @if($destination->image)
                                            <img src="{{ asset('storage/' . $destination->image) }}" 
                                                 alt="{{ $destination->name }}" 
                                                 class="rounded"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-geo-alt text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $destination->name }}</div>
                                        <small class="text-muted">{{ $destination->slug }}</small>
                                    </td>
                                    <td>
                                        @if($destination->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Pasif</span>
                                        @endif
                                        @if($destination->show_in_menu)
                                            <span class="badge bg-info">Menüde</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $destination->sort_order }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $destination->posts()->count() }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $destination->created_at->format('d.m.Y') }}</small><br>
                                        <small class="text-muted">{{ $destination->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('destinations.edit', $destination) }}" 
                                           class="btn btn-sm btn-outline-secondary me-2"
                                           title="Düzenle">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger delete-destination-btn" 
                                                data-destination-id="{{ $destination->id }}"
                                                title="Sil">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        
                                        <form id="delete-form-{{ $destination->id }}" action="{{ route('destinations.destroy', $destination) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-geo-alt display-4 text-muted"></i>
                                        <p class="text-muted mb-0 mt-2">Henüz destinasyon yok.</p>
                                        <a href="{{ route('destinations.create') }}" class="btn btn-sm btn-primary mt-2">
                                            İlk Destinasyonu Oluştur
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($destinations->hasPages())
        <div class="col-12 mt-4">
            <nav class="mt-7 mt-lg-10">
                <ul class="pagination mb-0">
                    @if ($destinations->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Önceki</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $destinations->previousPageUrl() }}">Önceki</a></li>
                    @endif

                    @php
                        $currentPage = $destinations->currentPage();
                        $lastPage = $destinations->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp

                    @if($startPage > 1)
                        <li class="page-item"><a class="page-link" href="{{ $destinations->url(1) }}">1</a></li>
                        @if($startPage > 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endif

                    @for ($page = $startPage; $page <= $endPage; $page++)
                        @if ($page == $currentPage)
                            <li class="page-item"><a class="page-link active" href="#!">{{ $page }}</a></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $destinations->url($page) }}">{{ $page }}</a></li>
                        @endif
                    @endfor

                    @if($endPage < $lastPage)
                        @if($endPage < $lastPage - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="{{ $destinations->url($lastPage) }}">{{ $lastPage }}</a></li>
                    @endif

                    @if ($destinations->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $destinations->nextPageUrl() }}">Sonraki</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Sonraki</span></li>
                    @endif
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-destination-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const destinationId = this.getAttribute('data-destination-id');
            if (confirm('Bu destinasyonu silmek istediğinize emin misiniz?')) {
                document.getElementById('delete-form-' + destinationId).submit();
            }
        });
    });
});
</script>
@endpush

