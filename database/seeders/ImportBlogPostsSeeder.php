<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class ImportBlogPostsSeeder extends Seeder
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
            
            // Blog tablosundan verileri çek
            $stmt = $fidanlikDb->query("SELECT id, baslik, sef, description, aciklama, img FROM blog");
            $blogPosts = $stmt->fetchAll();
            
            $this->command->info("Toplam " . count($blogPosts) . " blog yazısı bulundu.");
            
            $imported = 0;
            $skipped = 0;
            
            foreach ($blogPosts as $blogPost) {
                // Aynı ID'ye sahip bir post zaten varsa atla
                if (Post::where('id', $blogPost['id'])->exists()) {
                    $this->command->warn("ID {$blogPost['id']} zaten mevcut, atlanıyor...");
                    $skipped++;
                    continue;
                }
                
                // Slug'un unique olup olmadığını kontrol et
                $slug = $blogPost['sef'] ?? \Illuminate\Support\Str::slug($blogPost['baslik']);
                $originalSlug = $slug;
                $counter = 1;
                while (Post::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                
                // Görsel yolunu düzelt
                $coverImage = null;
                if (!empty($blogPost['img'])) {
                    $imgPath = $blogPost['img'];
                    // Eğer zaten "posts/" ile başlamıyorsa ekle
                    if (!str_starts_with($imgPath, 'posts/')) {
                        $coverImage = 'posts/' . $imgPath;
                    } else {
                        $coverImage = $imgPath;
                    }
                }
                
                // Post oluştur
                $post = new Post();
                $post->id = $blogPost['id'];
                $post->title = $blogPost['baslik'] ?? '';
                $post->slug = $slug;
                $post->content = $blogPost['aciklama'] ?? '';
                $post->meta_description = $blogPost['description'] ?? null;
                $post->cover_image = $coverImage;
                $post->user_id = 1;
                $post->is_published = true;
                $post->comments_enabled = false;
                $post->save();
                
                $imported++;
                $this->command->info("ID {$blogPost['id']} başarıyla aktarıldı: {$blogPost['baslik']}");
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
