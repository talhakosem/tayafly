<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class SitemapController extends Controller
{
    /**
     * Generate and return sitemap.xml
     */
    public function index()
    {
        $baseUrl = config('app.url');
        
        // Sitemap XML başlangıcı
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Ana sayfa
        $xml .= $this->urlElement($baseUrl . '/', '1.0', 'daily');
        
        // Blog listesi
        $xml .= $this->urlElement($baseUrl . '/blog', '0.8', 'daily');
        
        // Hakkımızda sayfası
        $xml .= $this->urlElement($baseUrl . '/hakkimizda', '0.6', 'monthly');
        
        // Yayınlanmış blog yazıları (kategori slug ile birlikte)
        $posts = Post::where('is_published', true)
            ->with('categories:categories.id,categories.slug')
            ->orderBy('updated_at', 'desc')
            ->get();
        
        foreach ($posts as $post) {
            $categorySlug = $post->categories->first()->slug ?? '';
            if ($categorySlug) {
                $lastmod = $post->updated_at ? $post->updated_at->format('Y-m-d') : date('Y-m-d');
                $xml .= $this->urlElement($baseUrl . '/' . $categorySlug . '/' . $post->slug, '0.7', 'weekly', $lastmod);
            }
        }
        
        // Kategoriler
        $categories = Category::orderBy('updated_at', 'desc')->get();
        
        foreach ($categories as $category) {
            $lastmod = $category->updated_at ? $category->updated_at->format('Y-m-d') : date('Y-m-d');
            $xml .= $this->urlElement($baseUrl . '/' . $category->slug, '0.7', 'weekly', $lastmod);
        }
        
        $xml .= '</urlset>';
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
    
    /**
     * Generate URL element for sitemap
     */
    private function urlElement($loc, $priority = '0.5', $changefreq = 'monthly', $lastmod = null)
    {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . "</loc>\n";
        $xml .= "    <priority>" . $priority . "</priority>\n";
        $xml .= "    <changefreq>" . $changefreq . "</changefreq>\n";
        
        if ($lastmod) {
            $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
        } else {
            $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        }
        
        $xml .= "  </url>\n";
        
        return $xml;
    }

    /**
     * Sitemap karşılaştırma sayfası
     */
    public function compare(Request $request)
    {
        $sitemapUrl = $request->input('url', 'https://www.fidanlik.com.tr/sitemap.xml');
        $results = null;
        $error = null;

        if ($request->has('compare')) {
            try {
                // Mevcut sitedeki sitemap'i çek
                $response = Http::timeout(30)->get($sitemapUrl);
                
                if (!$response->successful()) {
                    $error = "Sitemap çekilemedi. HTTP Status: " . $response->status();
                } else {
                    $xml = $response->body();
                    
                    // XML'den slug'ları çıkar
                    $existingSlugs = $this->extractSlugsFromSitemap($xml);
                    
                    // Bizim veritabanındaki slug'ları al
                    $ourSlugs = $this->getOurSlugs();
                    
                    // Karşılaştır
                    $missingSlugs = array_values(array_diff($existingSlugs, $ourSlugs));
                    $newSlugs = array_values(array_diff($ourSlugs, $existingSlugs));
                    
                    $results = [
                        'existing_count' => count($existingSlugs),
                        'our_count' => count($ourSlugs),
                        'missing_slugs' => $missingSlugs,
                        'new_slugs' => $newSlugs,
                        'missing_count' => count($missingSlugs),
                        'new_count' => count($newSlugs),
                    ];
                }
            } catch (\Exception $e) {
                $error = "Hata: " . $e->getMessage();
            }
        }

        return view('sitemap.compare', compact('results', 'error', 'sitemapUrl'));
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
        
        // Özel route'ları atla (blog, hakkimizda, vb.)
        $specialRoutes = ['blog', 'hakkimizda', 'dashboard', 'profile', 'login', 'register'];
        if (in_array($path, $specialRoutes)) {
            return null;
        }
        
        // Admin route'larını atla
        if (strpos($path, 'admin/') === 0 || 
            strpos($path, 'dashboard') === 0 ||
            strpos($path, 'posts/') === 0 ||
            strpos($path, 'categories/') === 0 ||
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
        
        // Kategoriler
        $categorySlugs = Category::whereNotNull('slug')
            ->pluck('slug')
            ->toArray();
        $slugs = array_merge($slugs, $categorySlugs);
        
        return array_unique($slugs);
    }
}

