<?php
/**
 * Storage Link Düzeltme Scripti
 * Kullanım: https://fidanlik.com.tr/fix-storage.php?pass=fix123&action=fix
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Güvenlik
$password = 'fix123';
if (!isset($_GET['pass']) || $_GET['pass'] !== $password) {
    die('Access denied. Kullanim: fix-storage.php?pass=fix123&action=check');
}

$projectRoot = dirname(__DIR__);
$publicStorage = __DIR__ . '/storage';
$targetStorage = $projectRoot . '/storage/app/public';

echo "<h1>Storage Link Düzeltme</h1>";
echo "<pre>";

// Mevcut durum
echo "=== MEVCUT DURUM ===\n";
echo "Proje yolu: $projectRoot\n";
echo "Public storage: $publicStorage\n";
echo "Target storage: $targetStorage\n\n";

if (file_exists($publicStorage)) {
    if (is_link($publicStorage)) {
        echo "✓ Symlink mevcut\n";
        echo "Hedef: " . readlink($publicStorage) . "\n";
    } else {
        echo "❌ Dizin var ama symlink değil!\n";
        
        if (isset($_GET['action']) && $_GET['action'] === 'fix') {
            echo "\n=== DÜZELTME YAPILIYOR ===\n";
            
            // Yedek al
            $backupDir = $publicStorage . '_backup_' . time();
            if (rename($publicStorage, $backupDir)) {
                echo "✓ Mevcut dizin yedeklendi: $backupDir\n";
            }
            
            // Symlink oluştur
            if (symlink($targetStorage, $publicStorage)) {
                echo "✓ Symlink başarıyla oluşturuldu!\n";
                echo "✓ Link: $publicStorage -> $targetStorage\n";
                
                // Kontrol
                if (is_link($publicStorage)) {
                    echo "\n<span style='color: green; font-weight: bold;'>✓✓✓ BAŞARILI! Artık blog görselleri yükleyebilirsiniz!</span>\n";
                }
            } else {
                echo "❌ Symlink oluşturulamadı!\n";
                echo "Hata: " . error_get_last()['message'] . "\n";
                echo "\nManuel çözüm:\n";
                echo "1. cPanel File Manager'dan public/storage klasörünü SİLİN\n";
                echo "2. cPanel Terminal'den: php artisan storage:link\n";
            }
        } else {
            echo "\n<a href='?pass=fix123&action=fix' style='display: inline-block; padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px;'>DÜZELT</a>\n";
        }
    }
} else {
    echo "❌ Storage dizini/linki yok!\n";
    
    if (isset($_GET['action']) && $_GET['action'] === 'fix') {
        echo "\n=== OLUŞTURULUYOR ===\n";
        if (symlink($targetStorage, $publicStorage)) {
            echo "✓ Symlink başarıyla oluşturuldu!\n";
            echo "<span style='color: green; font-weight: bold;'>✓✓✓ BAŞARILI!</span>\n";
        } else {
            echo "❌ Symlink oluşturulamadı: " . error_get_last()['message'] . "\n";
        }
    }
}

echo "</pre>";

echo "<hr>";
echo "<h2>Adımlar:</h2>";
echo "<ol>";
echo "<li>Yukarıdaki 'DÜZELT' butonuna tıklayın</li>";
echo "<li>Başarılı olduktan sonra blog'da görsel yüklemeyi test edin</li>";
echo "<li><strong>Bu dosyayı SİLİN!</strong> (fix-storage.php)</li>";
echo "</ol>";
?>
