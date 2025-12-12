<?php
/**
 * Sunucu Kontrol Scripti
 * Bu dosyayı sunucuda çalıştırarak sorunları tespit edebilirsiniz
 * Kullanım: https://yoursite.com/check-server.php
 */

// Güvenlik için basit şifre koruması
// ⚠️ KONTROL SONRASI BU DOSYAYI SİLİN!
$password = 'fidanlik2024'; // Şifreyi değiştirin
if (!isset($_GET['pass']) || $_GET['pass'] !== $password) {
    die('Access denied. Kullanim: check-server.php?pass=fidanlik2024');
}

echo "<h1>Sunucu Kontrol Raporu</h1>";
echo "<pre>";

// 1. PHP Versiyonu
echo "\n=== PHP VERSİYONU ===\n";
echo "PHP Version: " . phpversion() . "\n";

// 2. Upload Limitleri
echo "\n=== UPLOAD LİMİTLERİ ===\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";

// 3. Storage Klasörü Kontrolleri
echo "\n=== STORAGE KLASÖRÜ ===\n";

$storagePath = __DIR__ . '/../storage/app/public/posts';
echo "Storage path: $storagePath\n";
echo "Klasör var mı: " . (is_dir($storagePath) ? 'EVET' : 'HAYIR') . "\n";

if (is_dir($storagePath)) {
    echo "Yazılabilir mi: " . (is_writable($storagePath) ? 'EVET' : 'HAYIR') . "\n";
    echo "İzinler: " . substr(sprintf('%o', fileperms($storagePath)), -4) . "\n";
    
    // Dosya sayısı
    $files = glob($storagePath . '/*');
    echo "Dosya sayısı: " . count($files) . "\n";
}

// 4. Storage Link Kontrolü
echo "\n=== STORAGE LİNK ===\n";
$publicStoragePath = __DIR__ . '/storage';
echo "Public storage path: $publicStoragePath\n";
echo "Link var mı: " . (is_link($publicStoragePath) ? 'EVET' : 'HAYIR') . "\n";

if (is_link($publicStoragePath)) {
    echo "Link hedefi: " . readlink($publicStoragePath) . "\n";
} else if (is_dir($publicStoragePath)) {
    echo "⚠️ Storage dizini var ama symlink değil!\n";
} else {
    echo "❌ Storage linki/dizini bulunamadı!\n";
    echo "Çözüm: php artisan storage:link komutunu çalıştırın\n";
}

// 5. Yazılabilirlik Testi
echo "\n=== YAZILMA TESTİ ===\n";
$testFile = $storagePath . '/test_' . time() . '.txt';
try {
    if (is_dir($storagePath)) {
        $result = file_put_contents($testFile, 'test');
        if ($result !== false) {
            echo "✓ Test dosyası yazıldı\n";
            unlink($testFile);
            echo "✓ Test dosyası silindi\n";
        } else {
            echo "❌ Test dosyası yazılamadı!\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
}

// 6. Laravel Ortam Bilgileri
echo "\n=== LARAVEL ORTAM ===\n";
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    echo ".env dosyası: MEVCUT\n";
    
    // APP_ENV kontrol et
    $envContent = file_get_contents($envPath);
    if (preg_match('/APP_ENV=(.*)/', $envContent, $matches)) {
        echo "APP_ENV: " . trim($matches[1]) . "\n";
    }
    if (preg_match('/APP_DEBUG=(.*)/', $envContent, $matches)) {
        echo "APP_DEBUG: " . trim($matches[1]) . "\n";
    }
} else {
    echo "❌ .env dosyası bulunamadı!\n";
}

// 7. Yüklü PHP Eklentileri
echo "\n=== GEREKLİ PHP EKLENTİLERİ ===\n";
$required = ['fileinfo', 'gd', 'json', 'mbstring'];
foreach ($required as $ext) {
    echo "$ext: " . (extension_loaded($ext) ? '✓' : '❌') . "\n";
}

echo "\n=== KONTROL TAMAMLANDI ===\n";
echo "Tarih: " . date('Y-m-d H:i:s') . "\n";
echo "</pre>";

echo "<h2>ÖNERİLER</h2>";
echo "<ul>";
echo "<li>Eğer storage linki yoksa: <code>php artisan storage:link</code> çalıştırın</li>";
echo "<li>Eğer izin sorunu varsa: <code>chmod -R 775 storage/app/public/posts</code> çalıştırın</li>";
echo "<li>Eğer upload limiti düşükse: php.ini dosyasını düzenleyin veya hosting sağlayıcınıza başvurun</li>";
echo "<li>Cache temizleyin: <code>php artisan optimize:clear</code></li>";
echo "</ul>";

echo "<p><strong>⚠️ Kontrol sonrası bu dosyayı silin!</strong></p>";
?>

