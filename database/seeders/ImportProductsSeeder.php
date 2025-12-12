<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class ImportProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mevcut veritabanı bağlantı bilgilerini al
        $host = Config::get('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
        $port = Config::get('database.connections.mysql.port', env('DB_PORT', '3306'));
        $username = Config::get('database.connections.mysql.username', env('DB_USERNAME', 'root'));
        $password = Config::get('database.connections.mysql.password', env('DB_PASSWORD', ''));
        
        // Fidanlik veritabanına bağlan
        try {
            $fidanlikDb = new \PDO(
                "mysql:host={$host};port={$port};dbname=fidanlik;charset=utf8mb4",
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]
            );
            
            // Ürün tablosundan verileri çek
            $stmt = $fidanlikDb->query("SELECT id, baslik, sef, description, aciklama, minadet, eski_fiyat, fiyat, stok, stok_kodu, marka_id, teslimtarihi, zaman FROM urun");
            $products = $stmt->fetchAll();
            
            $this->command->info("Toplam " . count($products) . " ürün bulundu.");
            
            $imported = 0;
            $skipped = 0;
            
            foreach ($products as $product) {
                // Aynı ID'ye sahip bir ürün zaten varsa atla
                if (Product::where('id', $product['id'])->exists()) {
                    $this->command->warn("ID {$product['id']} zaten mevcut, atlanıyor...");
                    $skipped++;
                    continue;
                }
                
                // Slug'un unique olup olmadığını kontrol et
                $slug = $product['sef'] ?? \Illuminate\Support\Str::slug($product['baslik']);
                $originalSlug = $slug;
                $counter = 1;
                while (Product::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                
                // Ürün oluştur
                $newProduct = new Product();
                $newProduct->id = $product['id'];
                $newProduct->name = $product['baslik'] ?? '';
                $newProduct->slug = $slug;
                $newProduct->description = $product['aciklama'] ?? null;
                $newProduct->meta_description = $product['description'] ?? null;
                $newProduct->min_quantity = $product['minadet'] ?? 1;
                $newProduct->old_price = $product['eski_fiyat'] ?? null;
                $newProduct->price = $product['fiyat'] ?? 0;
                $newProduct->stock = $product['stok'] ?? 0;
                $newProduct->sku = !empty($product['stok_kodu']) ? $product['stok_kodu'] : null;
                $newProduct->brand_id = $product['marka_id'] > 0 ? $product['marka_id'] : null;
                $newProduct->delivery_date = !empty($product['teslimtarihi']) ? $product['teslimtarihi'] : null;
                $newProduct->is_active = true;
                
                // Zaman bilgisini kullan
                if (!empty($product['zaman'])) {
                    $newProduct->created_at = $product['zaman'];
                    $newProduct->updated_at = $product['zaman'];
                }
                
                $newProduct->save();
                
                $imported++;
                $this->command->info("✓ ID {$product['id']} başarıyla aktarıldı: {$product['baslik']}");
            }
            
            $this->command->info("\nÜrün aktarımı tamamlandı!");
            $this->command->info("Aktarılan: {$imported}");
            $this->command->info("Atlanan: {$skipped}");
            
            // Şimdi görselleri aktar
            $this->command->info("\nGörseller aktarılıyor...");
            
            $imgStmt = $fidanlikDb->query("SELECT id, urun_id, img, sira FROM urun_img");
            $productImages = $imgStmt->fetchAll();
            
            $this->command->info("Toplam " . count($productImages) . " görsel bulundu.");
            
            $imgImported = 0;
            $imgSkipped = 0;
            
            foreach ($productImages as $img) {
                // Ürün var mı kontrol et
                if (!Product::where('id', $img['urun_id'])->exists()) {
                    $imgSkipped++;
                    continue;
                }
                
                // Aynı ID'ye sahip bir görsel zaten varsa atla
                if (ProductImage::where('id', $img['id'])->exists()) {
                    $imgSkipped++;
                    continue;
                }
                
                // Görsel yolunu düzelt
                $imagePath = null;
                if (!empty($img['img'])) {
                    $imgPath = $img['img'];
                    // Eğer zaten "products/" ile başlamıyorsa ekle
                    if (!str_starts_with($imgPath, 'products/')) {
                        $imagePath = 'products/' . $imgPath;
                    } else {
                        $imagePath = $imgPath;
                    }
                }
                
                // Görsel oluştur
                $newImage = new ProductImage();
                $newImage->id = $img['id'];
                $newImage->product_id = $img['urun_id'];
                $newImage->image = $imagePath;
                $newImage->sort_order = $img['sira'] ?? 0;
                $newImage->save();
                
                $imgImported++;
            }
            
            $this->command->info("\nGörsel aktarımı tamamlandı!");
            $this->command->info("Aktarılan görsel: {$imgImported}");
            $this->command->info("Atlanan görsel: {$imgSkipped}");
            
        } catch (\PDOException $e) {
            $this->command->error("Veritabanı bağlantı hatası: " . $e->getMessage());
            $this->command->error("Lütfen .env dosyanızdaki DB_HOST, DB_USERNAME, DB_PASSWORD bilgilerinin doğru olduğundan emin olun.");
        } catch (\Exception $e) {
            $this->command->error("Hata: " . $e->getMessage());
        }
    }
}
