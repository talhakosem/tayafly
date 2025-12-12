<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Basit Upload Test Scripti
 * Kullanım: https://fidanlik.com.tr/test-upload.php
 */

// Güvenlik
$password = 'test123';
if (!isset($_GET['pass']) || $_GET['pass'] !== $password) {
    die('Access denied. Kullanim: test-upload.php?pass=test123');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Upload Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Upload Test</h1>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<h2>Upload Sonuçları</h2>";
    echo "<pre>";
    
    $file = $_FILES['test_file'];
    
    echo "=== DOSYA BİLGİLERİ ===\n";
    echo "Dosya Adı: " . $file['name'] . "\n";
    echo "Dosya Tipi: " . $file['type'] . "\n";
    echo "Dosya Boyutu: " . round($file['size'] / 1024, 2) . " KB\n";
    echo "Geçici Dosya: " . $file['tmp_name'] . "\n";
    echo "Hata Kodu: " . $file['error'] . "\n\n";
    
    // Hata kontrolü
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'Dosya php.ini upload_max_filesize limitini aşıyor',
            UPLOAD_ERR_FORM_SIZE => 'Dosya HTML MAX_FILE_SIZE limitini aşıyor',
            UPLOAD_ERR_PARTIAL => 'Dosya sadece kısmen yüklendi',
            UPLOAD_ERR_NO_FILE => 'Dosya yüklenmedi',
            UPLOAD_ERR_NO_TMP_DIR => 'Geçici klasör eksik',
            UPLOAD_ERR_CANT_WRITE => 'Dosya diske yazılamadı',
            UPLOAD_ERR_EXTENSION => 'PHP eklentisi upload'ı durdurdu'
        ];
        
        echo "<span class='error'>❌ HATA: " . ($errors[$file['error']] ?? 'Bilinmeyen hata') . "</span>\n";
    } else {
        echo "<span class='success'>✓ Dosya başarıyla yüklendi</span>\n\n";
        
        // Hedef klasör
        $targetDir = __DIR__ . '/../storage/app/public/posts/';
        $targetFile = $targetDir . 'test_' . time() . '_' . basename($file['name']);
        
        echo "=== KLASÖR KONTROL ===\n";
        echo "Hedef klasör: $targetDir\n";
        echo "Klasör var mı: " . (is_dir($targetDir) ? 'EVET' : 'HAYIR') . "\n";
        
        if (!is_dir($targetDir)) {
            echo "<span class='error'>❌ Klasör bulunamadı!</span>\n";
            echo "Çözüm: mkdir -p $targetDir\n";
        } else {
            echo "Yazılabilir mi: " . (is_writable($targetDir) ? 'EVET' : 'HAYIR') . "\n";
            echo "İzinler: " . substr(sprintf('%o', fileperms($targetDir)), -4) . "\n\n";
            
            // Dosyayı taşı
            echo "=== DOSYA TAŞIMA ===\n";
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                echo "<span class='success'>✓ Dosya başarıyla kaydedildi!</span>\n";
                echo "Konum: $targetFile\n";
                echo "Dosya boyutu: " . filesize($targetFile) . " bytes\n";
                
                // Dosyayı hemen sil
                unlink($targetFile);
                echo "\n<span class='success'>✓ Test dosyası silindi</span>\n";
            } else {
                echo "<span class='error'>❌ Dosya taşınamadı!</span>\n";
                echo "Olası nedenler:\n";
                echo "- Klasör yazma izni yok (chmod 775)\n";
                echo "- Klasör sahibi yanlış (chown www-data)\n";
                echo "- SELinux engelliyor\n";
            }
        }
    }
    
    echo "</pre>";
}
?>

    <h2>PHP Ayarları</h2>
    <pre><?php
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "file_uploads: " . (ini_get('file_uploads') ? 'ON' : 'OFF') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n\n";

echo "=== PHP EXTENSIONS ===\n";
echo "fileinfo: " . (extension_loaded('fileinfo') ? '✓' : '❌') . "\n";
echo "gd: " . (extension_loaded('gd') ? '✓' : '❌') . "\n";
echo "mbstring: " . (extension_loaded('mbstring') ? '✓' : '❌') . "\n";
    ?></pre>

    <h2>Upload Testi</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="test_file" accept="image/*" required>
        <button type="submit">Test Et</button>
    </form>

    <p><strong>⚠️ Test sonrası bu dosyayı silin!</strong></p>
</body>
</html>

