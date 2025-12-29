<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Dosyayı public disk'e kaydet ve public/storage'a da kopyala
     * Paylaşımlı hosting'de symlink çalışmadığı için kullanılır
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string|false
     */
    public static function storeAndCopy($file, $directory = '')
    {
        // Önce normal storage'a kaydet
        $path = $file->store($directory, 'public');
        
        if ($path) {
            // public/storage'a da kopyala
            self::copyToPublicStorage($path);
        }
        
        return $path;
    }
    
    /**
     * Storage'daki bir dosyayı public/storage'a kopyala
     *
     * @param string $storagePath storage/app/public içindeki yol
     * @return bool
     */
    public static function copyToPublicStorage($storagePath)
    {
        try {
            $sourcePath = storage_path('app/public/' . $storagePath);
            $targetPath = public_path('storage/' . $storagePath);
            
            // Hedef klasörü oluştur
            $targetDir = dirname($targetPath);
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            // Dosyayı kopyala
            if (file_exists($sourcePath)) {
                return copy($sourcePath, $targetPath);
            }
        } catch (\Exception $e) {
            \Log::error('Storage kopyalama hatası', [
                'path' => $storagePath,
                'error' => $e->getMessage()
            ]);
        }
        
        return false;
    }
    
    /**
     * Storage'dan dosya sil ve public/storage'dan da sil
     *
     * @param string $storagePath
     * @return bool
     */
    public static function deleteFromBoth($storagePath)
    {
        $deleted = false;
        
        // Storage'dan sil
        if (Storage::disk('public')->exists($storagePath)) {
            $deleted = Storage::disk('public')->delete($storagePath);
        }
        
        // public/storage'dan da sil
        $publicPath = public_path('storage/' . $storagePath);
        if (file_exists($publicPath)) {
            @unlink($publicPath);
        }
        
        return $deleted;
    }
}


