<?php
/**
 * Storage Link Oluşturucu Script
 * Kullanım: https://tayafly.com/fix-storage-link.php
 * 
 * ÖNEMLİ: Bu scripti çalıştırdıktan sonra SİLİN!
 */

// Laravel bootstrap
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Storage Link Oluştur</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: green; background: #d4edda; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px 10px 0; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Storage Link Oluştur</h1>

<?php
$storagePublicPath = storage_path('app/public');
$publicStoragePath = public_path('storage');
$success = false;
$method = '';

// Eğer form gönderildiyse
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'create_symlink') {
        // Symlink oluşturmayı dene
        try {
            // Eğer zaten varsa sil
            if (file_exists($publicStoragePath) || is_link($publicStoragePath)) {
                if (is_link($publicStoragePath)) {
                    unlink($publicStoragePath);
                } else {
                    // Klasör ise recursive sil
                    $files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($publicStoragePath, RecursiveDirectoryIterator::SKIP_DOTS),
                        RecursiveIteratorIterator::CHILD_FIRST
                    );
                    foreach ($files as $file) {
                        if ($file->isDir()) {
                            rmdir($file->getRealPath());
                        } else {
                            unlink($file->getRealPath());
                        }
                    }
                    rmdir($publicStoragePath);
                }
            }
            
            // Symlink oluştur
            if (function_exists('symlink')) {
                $target = '../storage/app/public';
                if (symlink($target, $publicStoragePath)) {
                    $success = true;
                    $method = 'symlink';
                    echo '<div class="success">✅ Symlink başarıyla oluşturuldu!</div>';
                } else {
                    throw new Exception('Symlink oluşturulamadı. Fonksiyon çalıştı ama başarısız oldu.');
                }
            } else {
                throw new Exception('symlink() fonksiyonu devre dışı. Paylaşımlı hosting\'de genellikle çalışmaz.');
            }
        } catch (Exception $e) {
            echo '<div class="warning">⚠️ Symlink oluşturulamadı: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<div class="info">Alternatif yöntem deneniyor: Dosya kopyalama...</div>';
            
            // Alternatif: Dosyaları kopyala
            try {
                if (!file_exists($publicStoragePath)) {
                    mkdir($publicStoragePath, 0755, true);
                }
                
                // Recursive copy
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($storagePublicPath, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );
                
                foreach ($iterator as $item) {
                    $targetPath = $publicStoragePath . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
                    
                    if ($item->isDir()) {
                        if (!file_exists($targetPath)) {
                            mkdir($targetPath, 0755, true);
                        }
                    } else {
                        copy($item->getRealPath(), $targetPath);
                    }
                }
                
                $success = true;
                $method = 'copy';
                echo '<div class="success">✅ Dosyalar başarıyla kopyalandı! (Symlink yerine)</div>';
                echo '<div class="warning">⚠️ Not: Bu bir kopya. Yeni dosyalar eklendiğinde tekrar kopyalamanız gerekebilir.</div>';
            } catch (Exception $e2) {
                echo '<div class="error">❌ Dosya kopyalama da başarısız: ' . htmlspecialchars($e2->getMessage()) . '</div>';
            }
        }
    } elseif ($action === 'copy_files') {
        // Doğrudan kopyalama
        try {
            if (!file_exists($publicStoragePath)) {
                mkdir($publicStoragePath, 0755, true);
            }
            
            // Recursive copy
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($storagePublicPath, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            
            $fileCount = 0;
            foreach ($iterator as $item) {
                $targetPath = $publicStoragePath . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
                
                if ($item->isDir()) {
                    if (!file_exists($targetPath)) {
                        mkdir($targetPath, 0755, true);
                    }
                } else {
                    copy($item->getRealPath(), $targetPath);
                    $fileCount++;
                }
            }
            
            $success = true;
            $method = 'copy';
            echo '<div class="success">✅ ' . $fileCount . ' dosya başarıyla kopyalandı!</div>';
            echo '<div class="info">💡 Yeni dosya yüklediğinizde bu scripti tekrar çalıştırmalısınız.</div>';
        } catch (Exception $e) {
            echo '<div class="error">❌ Dosya kopyalama hatası: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Mevcut durumu kontrol et
if (!$success) {
    echo '<h2>Mevcut Durum</h2>';
    
    if (file_exists($publicStoragePath) || is_link($publicStoragePath)) {
        if (is_link($publicStoragePath)) {
            echo '<div class="info">✅ public/storage zaten bir symlink</div>';
            $linkTarget = readlink($publicStoragePath);
            echo '<div class="info">Hedef: <code>' . $linkTarget . '</code></div>';
        } else {
            echo '<div class="warning">⚠️ public/storage bir klasör (symlink değil)</div>';
        }
    } else {
        echo '<div class="error">❌ public/storage bulunamadı</div>';
    }
    
    echo '<h2>Çözüm</h2>';
    echo '<div class="info">';
    echo 'İki yöntem var:<br><br>';
    echo '<strong>Yöntem 1:</strong> Symlink oluştur (eğer sunucu izin veriyorsa)<br>';
    echo '<strong>Yöntem 2:</strong> Dosyaları kopyala (her zaman çalışır, ama yeni dosyalarda tekrar kopyalama gerekir)';
    echo '</div>';
    
    ?>
    <form method="POST" style="margin: 20px 0;">
        <button type="submit" name="action" value="create_symlink" class="btn">
            🔗 Symlink Oluştur (Önce Denenecek)
        </button>
        <button type="submit" name="action" value="copy_files" class="btn" style="background: #28a745;">
            📁 Dosyaları Kopyala (Güvenli Yöntem)
        </button>
    </form>
    <?php
} else {
    // Başarılı
    echo '<h2>✅ İşlem Tamamlandı!</h2>';
    
    if ($method === 'symlink') {
        echo '<div class="success">';
        echo '<strong>Symlink başarıyla oluşturuldu.</strong><br>';
        echo 'Artık dosyalarınız erişilebilir olmalı.';
        echo '</div>';
    } else {
        echo '<div class="success">';
        echo '<strong>Dosyalar başarıyla kopyalandı.</strong><br>';
        echo 'Not: Yeni dosya yüklediğinizde bu scripti tekrar çalıştırmalısınız.';
        echo '</div>';
    }
    
    // Test et
    $testLogo = $publicStoragePath . '/settings/cVzOJxes2IyrpgkQKGD11KHdynNUNZDDRVJhl7c0.png';
    if (file_exists($testLogo)) {
        echo '<div class="info">';
        echo '<strong>Test:</strong> Logo dosyası erişilebilir<br>';
        $testUrl = asset('storage/settings/cVzOJxes2IyrpgkQKGD11KHdynNUNZDDRVJhl7c0.png');
        echo '<a href="' . $testUrl . '" target="_blank">Logo\'yu görüntüle</a>';
        echo '</div>';
    }
}

echo '<div class="error" style="margin-top: 30px;">';
echo '<strong>⚠️ GÜVENLİK UYARISI:</strong><br>';
echo 'Bu scripti çalıştırdıktan sonra mutlaka <code>fix-storage-link.php</code> dosyasını SİLİN!';
echo '</div>';
?>

    </div>
</body>
</html>

