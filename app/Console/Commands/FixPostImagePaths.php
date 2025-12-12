<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixPostImagePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:fix-image-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post görsel yollarını düzeltir (posts/ prefix ekler)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $posts = Post::whereNotNull('cover_image')->get();
        
        $this->info("Toplam " . $posts->count() . " post bulundu.");
        
        $fixed = 0;
        $notFound = 0;
        
        foreach ($posts as $post) {
            $currentPath = $post->cover_image;
            
            // Eğer zaten "posts/" ile başlıyorsa atla
            if (str_starts_with($currentPath, 'posts/')) {
                continue;
            }
            
            // Eğer boşsa atla
            if (empty($currentPath)) {
                continue;
            }
            
            // Yeni yol
            $newPath = 'posts/' . $currentPath;
            
            // Dosyanın gerçekten var olup olmadığını kontrol et
            if (Storage::disk('public')->exists($newPath)) {
                $post->cover_image = $newPath;
                $post->save();
                $fixed++;
                $this->info("✓ ID {$post->id}: {$currentPath} -> {$newPath}");
            } else {
                // Eski yolda da kontrol et
                if (Storage::disk('public')->exists($currentPath)) {
                    $post->cover_image = $newPath;
                    $post->save();
                    $fixed++;
                    $this->info("✓ ID {$post->id}: {$currentPath} -> {$newPath} (taşındı)");
                } else {
                    $notFound++;
                    $this->warn("✗ ID {$post->id}: Görsel bulunamadı - {$currentPath}");
                }
            }
        }
        
        $this->info("\nİşlem tamamlandı!");
        $this->info("Düzeltilen: {$fixed}");
        $this->info("Bulunamayan: {$notFound}");
        
        return Command::SUCCESS;
    }
}
