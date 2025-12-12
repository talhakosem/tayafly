<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ImportProductCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $host = Config::get('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
        $port = Config::get('database.connections.mysql.port', env('DB_PORT', '3306'));
        $username = Config::get('database.connections.mysql.username', env('DB_USERNAME', 'root'));
        $password = Config::get('database.connections.mysql.password', env('DB_PASSWORD', ''));

        try {
            $fidanlikDb = new \PDO(
                "mysql:host={$host};port={$port};dbname=fidanlik;charset=utf8mb4",
                $username,
                $password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]
            );

            // urun_kategori tablosundan verileri çek
            $stmt = $fidanlikDb->query("SELECT urun_id, kategori_id FROM urun_kategori");
            $productCategories = $stmt->fetchAll();

            $this->command->info("Toplam " . count($productCategories) . " ürün-kategori ilişkisi bulundu.");

            $imported = 0;
            $skipped = 0;

            foreach ($productCategories as $pc) {
                $productId = $pc['urun_id'];
                $categoryId = $pc['kategori_id'];

                // Ürün ve kategori var mı kontrol et
                $productExists = DB::table('products')->where('id', $productId)->exists();
                $categoryExists = DB::table('categories')->where('id', $categoryId)->exists();

                if (!$productExists || !$categoryExists) {
                    $this->command->warn("Ürün ID {$productId} veya Kategori ID {$categoryId} bulunamadı, atlanıyor...");
                    $skipped++;
                    continue;
                }

                // Aynı ilişki zaten varsa atla
                $exists = DB::table('product_category')
                    ->where('product_id', $productId)
                    ->where('category_id', $categoryId)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // İlişkiyi ekle
                DB::table('product_category')->insert([
                    'product_id' => $productId,
                    'category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $imported++;
            }

            $this->command->info("\nAktarım tamamlandı!");
            $this->command->info("Aktarılan: {$imported}");
            $this->command->info("Atlanan: {$skipped}");

        } catch (\PDOException $e) {
            $this->command->error("Veritabanı bağlantı hatası: " . $e->getMessage());
        } catch (\Exception $e) {
            $this->command->error("Hata: " . $e->getMessage());
        }
    }
}
