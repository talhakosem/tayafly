<?php
/**
 * Storage Link Kontrol Scripti
 * Kullanım: https://tayafly.com/check-storage.php
 * 
 * ÖNEMLİ: Test sonrası bu dosyayı SİLİN!
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
    <title>Storage Kontrol</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📁 Storage Link Kontrolü</h1>

<?php
use Illuminate\Support\Facades\Storage;

echo '<h2>1. Storage Klasör Yapısı</h2>';

$storagePublicPath = storage_path('app/public');
$publicStoragePath = public_path('storage');

echo '<div class="info">';
echo '<strong>Storage Public Path:</strong> <code>' . $storagePublicPath . '</code><br>';
echo '<strong>Public Storage Path:</strong> <code>' . $publicStoragePath . '</code>';
echo '</div>';

// Storage/app/public klasörü var mı?
if (!file_exists($storagePublicPath)) {
    echo '<div class="error">❌ storage/app/public klasörü bulunamadı!</div>';
    echo '<div class="info">💡 Klasörü oluşturun: <code>mkdir -p storage/app/public</code></div>';
} else {
    echo '<div class="success">✅ storage/app/public klasörü mevcut</div>';
}

// Settings klasörü var mı?
$settingsPath = $storagePublicPath . '/settings';
if (!file_exists($settingsPath)) {
    echo '<div class="warning">⚠️ storage/app/public/settings klasörü yok (oluşturulacak)</div>';
} else {
    echo '<div class="success">✅ storage/app/public/settings klasörü mevcut</div>';
    
    // Klasördeki dosyaları listele
    $files = array_diff(scandir($settingsPath), ['.', '..']);
    if (count($files) > 0) {
        echo '<div class="info"><strong>Klasördeki dosyalar:</strong><ul>';
        foreach ($files as $file) {
            $filePath = $settingsPath . '/' . $file;
            $size = filesize($filePath);
            echo '<li>' . $file . ' (' . round($size / 1024, 2) . ' KB)</li>';
        }
        echo '</ul></div>';
    } else {
        echo '<div class="warning">⚠️ Klasör boş (henüz dosya yüklenmemiş)</div>';
    }
}

echo '<h2>2. Storage Link Kontrolü</h2>';

// Public/storage link var mı?
if (!file_exists($publicStoragePath)) {
    echo '<div class="error">❌ public/storage link bulunamadı!</div>';
    echo '<div class="warning"><strong>Çözüm (SSH varsa):</strong><br>';
    echo '<code>php artisan storage:link</code></div>';
    echo '<div class="warning"><strong>Çözüm (SSH yoksa):</strong><br>';
    echo 'Hosting panelinde File Manager\'dan manuel symlink oluşturun:<br>';
    echo '<code>public/storage</code> → <code>../storage/app/public</code><br>';
    echo 'VEYA hosting panelinde Terminal varsa:<br>';
    echo '<code>ln -s ../storage/app/public public/storage</code>';
    echo '</div>';
} else {
    echo '<div class="success">✅ public/storage mevcut</div>';
    
    // Symlink mi klasör mü?
    if (is_link($publicStoragePath)) {
        echo '<div class="success">✅ public/storage bir symlink (doğru)</div>';
        $linkTarget = readlink($publicStoragePath);
        echo '<div class="info">Symlink hedefi: <code>' . $linkTarget . '</code></div>';
        
        // Symlink çalışıyor mu?
        if (file_exists($publicStoragePath . '/settings')) {
            echo '<div class="success">✅ Symlink çalışıyor (settings klasörü erişilebilir)</div>';
        } else {
            echo '<div class="error">❌ Symlink çalışmıyor (settings klasörüne erişilemiyor)</div>';
        }
    } else {
        echo '<div class="warning">⚠️ public/storage bir klasör (symlink değil)</div>';
        echo '<div class="info">Eğer bu bir klasörse, silip symlink oluşturmanız gerekir.</div>';
    }
}

echo '<h2>3. Veritabanındaki Ayarlar</h2>';

try {
    $setting = \App\Models\Setting::getSettings();
    
    echo '<div class="info">';
    echo '<strong>Logo:</strong> ' . ($setting->logo ?: 'Yok') . '<br>';
    echo '<strong>Favicon:</strong> ' . ($setting->favicon ?: 'Yok');
    echo '</div>';
    
    if ($setting->logo) {
        $logoPath = storage_path('app/public/' . $setting->logo);
        if (file_exists($logoPath)) {
            echo '<div class="success">✅ Logo dosyası fiziksel olarak mevcut</div>';
            $logoUrl = asset('storage/' . $setting->logo);
            echo '<div class="info">Logo URL: <a href="' . $logoUrl . '" target="_blank">' . $logoUrl . '</a></div>';
            echo '<div><img src="' . $logoUrl . '" alt="Logo" style="max-width: 200px; border: 1px solid #ddd; padding: 5px;"></div>';
        } else {
            echo '<div class="error">❌ Logo dosyası fiziksel olarak bulunamadı: <code>' . $logoPath . '</code></div>';
        }
    }
    
    if ($setting->favicon) {
        $faviconPath = storage_path('app/public/' . $setting->favicon);
        if (file_exists($faviconPath)) {
            echo '<div class="success">✅ Favicon dosyası fiziksel olarak mevcut</div>';
            $faviconUrl = asset('storage/' . $setting->favicon);
            echo '<div class="info">Favicon URL: <a href="' . $faviconUrl . '" target="_blank">' . $faviconUrl . '</a></div>';
            echo '<div><img src="' . $faviconUrl . '" alt="Favicon" style="max-width: 50px; border: 1px solid #ddd; padding: 5px;"></div>';
        } else {
            echo '<div class="error">❌ Favicon dosyası fiziksel olarak bulunamadı: <code>' . $faviconPath . '</code></div>';
        }
    }
    
} catch (\Exception $e) {
    echo '<div class="error">❌ Veritabanı hatası: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

echo '<h2>4. Manuel Çözüm (SSH Yoksa)</h2>';
echo '<div class="info">';
echo 'Eğer hosting panelinde Terminal/SSH yoksa:<br><br>';
echo '<strong>Seçenek 1:</strong> File Manager\'da manuel symlink oluşturun<br>';
echo '<strong>Seçenek 2:</strong> public/storage klasörünü oluşturup, storage/app/public içeriğini kopyalayın (simetrik tutun)<br>';
echo '<strong>Seçenek 3:</strong> Hosting sağlayıcınızdan SSH erişimi isteyin<br>';
echo '</div>';

echo '<div class="error" style="margin-top: 20px;">';
echo '<strong>⚠️ GÜVENLİK UYARISI:</strong><br>';
echo 'Test sonrası bu dosyayı (check-storage.php) mutlaka SİLİN!';
echo '</div>';
?>

    </div>
</body>
</html>


