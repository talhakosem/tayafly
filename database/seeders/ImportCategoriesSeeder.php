<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class ImportCategoriesSeeder extends Seeder
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
            
            // Kategori tablosundan verileri çek
            $stmt = $fidanlikDb->query("SELECT id, baslik, sef, description, title, img, icon, aciklama, ust_menu, alt_menu, ust_kategori, sira, zaman FROM kategori");
            $categories = $stmt->fetchAll();
            
            $this->command->info("Toplam " . count($categories) . " kategori bulundu.");
            
            $imported = 0;
            $skipped = 0;
            
            foreach ($categories as $category) {
                // Aynı ID'ye sahip bir kategori zaten varsa atla
                if (Category::where('id', $category['id'])->exists()) {
                    $this->command->warn("ID {$category['id']} zaten mevcut, atlanıyor...");
                    $skipped++;
                    continue;
                }
                
                // Slug'un unique olup olmadığını kontrol et
                $slug = $category['sef'] ?? \Illuminate\Support\Str::slug($category['baslik']);
                $originalSlug = $slug;
                $counter = 1;
                while (Category::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                
                // Parent ID'yi kontrol et (0 ise null yap)
                $parentId = $category['ust_kategori'] > 0 ? $category['ust_kategori'] : 0;
                
                // Kategori oluştur
                $newCategory = new Category();
                $newCategory->id = $category['id'];
                $newCategory->name = $category['baslik'] ?? '';
                $newCategory->slug = $slug;
                $newCategory->description = $category['aciklama'] ?? null;
                $newCategory->meta_description = $category['description'] ?? null;
                $newCategory->meta_title = $category['title'] ?? null;
                $newCategory->image = $category['img'] ?? null;
                $newCategory->icon = $category['icon'] ?? null;
                $newCategory->parent_id = $parentId;
                $newCategory->sort_order = $category['sira'] ?? 0;
                $newCategory->show_in_top_menu = (bool)($category['ust_menu'] ?? 0);
                $newCategory->show_in_footer_menu = (bool)($category['alt_menu'] ?? 0);
                
                // Zaman bilgisini kullan
                if (!empty($category['zaman'])) {
                    $newCategory->created_at = $category['zaman'];
                    $newCategory->updated_at = $category['zaman'];
                }
                
                $newCategory->save();
                
                $imported++;
                $this->command->info("✓ ID {$category['id']} başarıyla aktarıldı: {$category['baslik']}");
            }
            
            $this->command->info("\nAktarım tamamlandı!");
            $this->command->info("Aktarılan: {$imported}");
            $this->command->info("Atlanan: {$skipped}");
            
        } catch (\PDOException $e) {
            $this->command->error("Veritabanı bağlantı hatası: " . $e->getMessage());
            $this->command->error("Lütfen .env dosyanızdaki DB_HOST, DB_USERNAME, DB_PASSWORD bilgilerinin doğru olduğundan emin olun.");
        } catch (\Exception $e) {
            $this->command->error("Hata: " . $e->getMessage());
        }
    }
}
