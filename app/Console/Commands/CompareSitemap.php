<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CompareSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:compare {url=https://www.fidanlik.com.tr/sitemap.xml}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mevcut sitedeki sitemap ile yeni sitemap\'i karşılaştırır ve eksik linkleri gösterir';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sitemapUrl = $this->argument('url');
        
        $this->info("Sitemap karşılaştırması başlatılıyor...");
        $this->info("Kaynak: {$sitemapUrl}\n");

        // Mevcut sitedeki sitemap'i çek
        $this->info("1. Mevcut sitedeki sitemap çekiliyor...");
        try {
            $response = Http::timeout(30)->get($sitemapUrl);
            
            if (!$response->successful()) {
                $this->error("Sitemap çekilemedi. HTTP Status: " . $response->status());
                return Command::FAILURE;
            }
            
            $xml = $response->body();
        } catch (\Exception $e) {
            $this->error("Sitemap çekilirken hata oluştu: " . $e->getMessage());
            return Command::FAILURE;
        }

        // XML'den slug'ları çıkar
        $this->info("2. Slug'lar çıkarılıyor...");
        $existingSlugs = $this->extractSlugsFromSitemap($xml);
        $this->info("   Mevcut sitede bulunan slug sayısı: " . count($existingSlugs));

        // Bizim veritabanındaki slug'ları al
        $this->info("3. Veritabanındaki slug'lar alınıyor...");
        $ourSlugs = $this->getOurSlugs();
        $this->info("   Bizim veritabanımızdaki slug sayısı: " . count($ourSlugs));

        // Karşılaştır
        $this->info("4. Karşılaştırma yapılıyor...\n");
        $missingSlugs = array_diff($existingSlugs, $ourSlugs);
        $newSlugs = array_diff($ourSlugs, $existingSlugs);

        // Sonuçları göster
        $this->displayResults($missingSlugs, $newSlugs, $existingSlugs, $ourSlugs);

        return Command::SUCCESS;
    }

    /**
     * XML sitemap'ten slug'ları çıkarır
     */
    private function extractSlugsFromSitemap(string $xml): array
    {
        $slugs = [];
        
        // XML'i parse et
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        
        // Tüm <loc> elementlerini bul
        $locations = $xpath->query('//sm:loc');
        
        foreach ($locations as $location) {
            $url = trim($location->textContent);
            
            // URL'den slug'ı çıkar
            $slug = $this->extractSlugFromUrl($url);
            
            if ($slug && !empty($slug)) {
                $slugs[] = $slug;
            }
        }
        
        return array_unique($slugs);
    }

    /**
     * URL'den slug çıkarır
     */
    private function extractSlugFromUrl(string $url): ?string
    {
        // URL'yi parse et
        $parsed = parse_url($url);
        
        if (!isset($parsed['path'])) {
            return null;
        }
        
        $path = trim($parsed['path'], '/');
        
        // Ana sayfa ise null döndür
        if (empty($path) || $path === '/') {
            return null;
        }
        
        // Özel route'ları atla (blog, urunler, hakkimizda, vb.)
        $specialRoutes = ['blog', 'urunler', 'hakkimizda', 'dashboard', 'profile', 'login', 'register'];
        if (in_array($path, $specialRoutes)) {
            return null;
        }
        
        // Admin route'larını atla
        if (strpos($path, 'admin/') === 0 || 
            strpos($path, 'dashboard') === 0 ||
            strpos($path, 'posts/') === 0 ||
            strpos($path, 'categories/') === 0 ||
            strpos($path, 'products/') === 0 ||
            strpos($path, 'orders/') === 0 ||
            strpos($path, 'settings') === 0) {
            return null;
        }
        
        return $path;
    }

    /**
     * Bizim veritabanındaki slug'ları alır
     */
    private function getOurSlugs(): array
    {
        $slugs = [];
        
        // Blog yazıları (yayınlanmış)
        $postSlugs = Post::where('is_published', true)
            ->whereNotNull('slug')
            ->pluck('slug')
            ->toArray();
        $slugs = array_merge($slugs, $postSlugs);
        
        // Ürünler (aktif)
        $productSlugs = Product::where('is_active', true)
            ->whereNotNull('slug')
            ->pluck('slug')
            ->toArray();
        $slugs = array_merge($slugs, $productSlugs);
        
        // Kategoriler
        $categorySlugs = Category::whereNotNull('slug')
            ->pluck('slug')
            ->toArray();
        $slugs = array_merge($slugs, $categorySlugs);
        
        return array_unique($slugs);
    }

    /**
     * Sonuçları gösterir
     */
    private function displayResults(array $missingSlugs, array $newSlugs, array $existingSlugs, array $ourSlugs): void
    {
        $this->line("═══════════════════════════════════════════════════════════");
        $this->info("KARŞILAŞTIRMA SONUÇLARI");
        $this->line("═══════════════════════════════════════════════════════════\n");
        
        // Eksik slug'lar (mevcut sitede var ama bizde yok)
        $this->warn("EKSİK SLUG'LAR (" . count($missingSlugs) . " adet):");
        $this->line("───────────────────────────────────────────────────────────");
        
        if (count($missingSlugs) > 0) {
            $this->table(
                ['#', 'Slug'],
                array_map(function($slug, $index) {
                    return [$index + 1, $slug];
                }, $missingSlugs, array_keys($missingSlugs))
            );
            
            // Dosyaya kaydet
            $filename = storage_path('logs/missing_slugs_' . date('Y-m-d_His') . '.txt');
            file_put_contents($filename, implode("\n", $missingSlugs));
            $this->info("\n✓ Eksik slug'lar dosyaya kaydedildi: {$filename}");
        } else {
            $this->info("✓ Eksik slug yok! Tüm slug'lar mevcut.");
        }
        
        $this->line("");
        
        // Yeni slug'lar (bizde var ama mevcut sitede yok)
        $this->info("YENİ SLUG'LAR (" . count($newSlugs) . " adet):");
        $this->line("───────────────────────────────────────────────────────────");
        
        if (count($newSlugs) > 0) {
            $this->table(
                ['#', 'Slug'],
                array_map(function($slug, $index) {
                    return [$index + 1, $slug];
                }, $newSlugs, array_keys($newSlugs))
            );
        } else {
            $this->info("Yeni slug yok.");
        }
        
        $this->line("");
        $this->line("═══════════════════════════════════════════════════════════");
        $this->info("ÖZET:");
        $this->line("  • Mevcut sitedeki toplam slug: " . count($existingSlugs));
        $this->line("  • Bizim veritabanımızdaki toplam slug: " . count($ourSlugs));
        $this->line("  • Eksik slug sayısı: " . count($missingSlugs));
        $this->line("  • Yeni slug sayısı: " . count($newSlugs));
        $this->line("═══════════════════════════════════════════════════════════");
    }
}


