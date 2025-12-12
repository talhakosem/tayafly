<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanUnusedPostImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:clean-unused-images {--force : Onay istemeden sil}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kullanılmayan post görsellerini siler';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Veritabanındaki kullanılan görselleri al
        $usedImages = Post::whereNotNull('cover_image')
            ->pluck('cover_image')
            ->map(function ($path) {
                // "posts/1.jpg" formatından sadece dosya adını al
                return basename($path);
            })
            ->unique()
            ->toArray();
        
        $this->info("Veritabanında kullanılan görsel sayısı: " . count($usedImages));
        
        // Storage'daki tüm dosyaları al
        $allFiles = Storage::disk('public')->files('posts');
        $allFileNames = array_map('basename', $allFiles);
        
        $this->info("Storage'da toplam dosya sayısı: " . count($allFileNames));
        
        // Kullanılmayan dosyaları bul
        $unusedFiles = array_diff($allFileNames, $usedImages);
        
        $this->info("Kullanılmayan dosya sayısı: " . count($unusedFiles));
        
        if (count($unusedFiles) == 0) {
            $this->info("Silinecek dosya yok.");
            return Command::SUCCESS;
        }
        
        // Onay iste (force flag yoksa)
        if (!$this->option('force')) {
            if (!$this->confirm('Kullanılmayan ' . count($unusedFiles) . ' dosyayı silmek istediğinize emin misiniz?')) {
                $this->info("İşlem iptal edildi.");
                return Command::SUCCESS;
            }
        }
        
        $deleted = 0;
        $errors = 0;
        
        foreach ($unusedFiles as $fileName) {
            $filePath = 'posts/' . $fileName;
            try {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                    $deleted++;
                    $this->info("✓ Silindi: {$fileName}");
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("✗ Hata ({$fileName}): " . $e->getMessage());
            }
        }
        
        $this->info("\nİşlem tamamlandı!");
        $this->info("Silinen: {$deleted}");
        if ($errors > 0) {
            $this->warn("Hata: {$errors}");
        }
        
        return Command::SUCCESS;
    }
}
