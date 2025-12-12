@extends('layouts.admin')

@section('title', 'Sitemap Karşılaştırma')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Sitemap Karşılaştırma</h4>
                <p class="text-muted mb-0">Mevcut sitedeki sitemap ile veritabanınızdaki slug'ları karşılaştırın</p>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('sitemap.compare') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-10">
                            <label for="url" class="form-label">Sitemap URL</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="url" 
                                   name="url" 
                                   value="{{ $sitemapUrl }}" 
                                   placeholder="https://www.fidanlik.com.tr/sitemap.xml"
                                   required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="compare" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i> Karşılaştır
                            </button>
                        </div>
                    </div>
                </form>

                @if($error)
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ $error }}
                    </div>
                @endif

                @if($results)
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $results['existing_count'] }}</h3>
                                    <p class="mb-0">Mevcut Sitedeki Slug</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $results['our_count'] }}</h3>
                                    <p class="mb-0">Bizim Veritabanımızdaki Slug</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $results['missing_count'] }}</h3>
                                    <p class="mb-0">Eksik Slug</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $results['new_count'] }}</h3>
                                    <p class="mb-0">Yeni Slug</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(count($results['missing_slugs']) > 0)
                        <div class="card mb-4 border-warning">
                            <div class="card-header bg-warning text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Eksik Slug'lar ({{ count($results['missing_slugs']) }} adet)
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Mevcut sitede var ama veritabanınızda olmayan slug'lar:</p>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th width="50">#</th>
                                                <th>Slug</th>
                                                <th width="150">İşlem</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($results['missing_slugs'] as $index => $slug)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><code>{{ $slug }}</code></td>
                                                    <td>
                                                        <a href="https://www.fidanlik.com.tr/{{ $slug }}" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-box-arrow-up-right"></i> Görüntüle
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <button onclick="copyMissingSlugs()" class="btn btn-sm btn-secondary">
                                        <i class="bi bi-clipboard me-1"></i> Eksik Slug'ları Kopyala
                                    </button>
                                    <textarea id="missingSlugsText" class="d-none">{{ implode("\n", $results['missing_slugs']) }}</textarea>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Harika!</strong> Eksik slug yok. Tüm slug'lar mevcut.
                        </div>
                    @endif

                    @if(count($results['new_slugs']) > 0)
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Yeni Slug'lar ({{ count($results['new_slugs']) }} adet)
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    <strong>Açıklama:</strong> Bu slug'lar yeni veritabanınızda (Laravel) mevcut ama eski sitede (fidanlik.com.tr) henüz yok. 
                                    Yani yeni eklediğiniz ürünler, blog yazıları veya kategoriler. Bu normal bir durum, yeni içerikleriniz olduğunu gösterir.
                                </p>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th width="50">#</th>
                                                <th>Slug</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($results['new_slugs'] as $index => $slug)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><code>{{ $slug }}</code></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function copyMissingSlugs() {
    const textarea = document.getElementById('missingSlugsText');
    textarea.classList.remove('d-none');
    textarea.select();
    document.execCommand('copy');
    textarea.classList.add('d-none');
    
    // Toast notification (eğer varsa) veya alert
    alert('Eksik slug\'lar panoya kopyalandı!');
}
</script>
@endsection

