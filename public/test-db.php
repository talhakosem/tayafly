<?php
/**
 * Veritabanı Bağlantı Test Scripti
 * Kullanım: https://tayafly.com/test-db.php
 * 
 * ÖNEMLİ: Test sonrası bu dosyayı SİLİN!
 */

// Güvenlik - Basit şifre koruması (geçici olarak kapatıldı)
// $password = 'test123';
// if (!isset($_GET['pass']) || $_GET['pass'] !== $password) {
//     die('Access denied. Kullanim: test-db.php?pass=test123');
// }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Veritabanı Bağlantı Testi</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Veritabanı Bağlantı Testi</h1>

<?php
// Laravel bootstrap
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo '<h2>1. .env Dosyası Kontrolü</h2>';

$envPath = __DIR__.'/../.env';
if (!file_exists($envPath)) {
    echo '<div class="error">❌ .env dosyası bulunamadı!</div>';
    echo '<div class="info">💡 .env.example dosyasını kopyalayıp .env yapın ve düzenleyin.</div>';
} else {
    echo '<div class="success">✅ .env dosyası mevcut</div>';
    
    // .env değerlerini oku
    $envContent = file_get_contents($envPath);
    $envVars = [];
    foreach (explode("\n", $envContent) as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $envVars[trim($key)] = trim($value);
        }
    }
    
    echo '<h3>Veritabanı Ayarları:</h3>';
    echo '<table>';
    echo '<tr><th>Ayar</th><th>Değer</th></tr>';
    echo '<tr><td>DB_CONNECTION</td><td>' . ($envVars['DB_CONNECTION'] ?? 'YOK') . '</td></tr>';
    echo '<tr><td>DB_HOST</td><td>' . ($envVars['DB_HOST'] ?? 'YOK') . '</td></tr>';
    echo '<tr><td>DB_PORT</td><td>' . ($envVars['DB_PORT'] ?? 'YOK') . '</td></tr>';
    echo '<tr><td>DB_DATABASE</td><td>' . ($envVars['DB_DATABASE'] ?? 'YOK') . '</td></tr>';
    echo '<tr><td>DB_USERNAME</td><td>' . ($envVars['DB_USERNAME'] ?? 'YOK') . '</td></tr>';
    echo '<tr><td>DB_PASSWORD</td><td>' . (isset($envVars['DB_PASSWORD']) ? '***' . substr($envVars['DB_PASSWORD'], -3) : 'YOK') . '</td></tr>';
    echo '</table>';
}

echo '<h2>2. Veritabanı Bağlantı Testi</h2>';

try {
    $db = \Illuminate\Support\Facades\DB::connection();
    $db->getPdo();
    
    echo '<div class="success">✅ Veritabanı bağlantısı başarılı!</div>';
    
    // Veritabanı bilgilerini göster
    $config = $db->getConfig();
    echo '<h3>Bağlantı Bilgileri:</h3>';
    echo '<table>';
    echo '<tr><th>Özellik</th><th>Değer</th></tr>';
    echo '<tr><td>Driver</td><td>' . $config['driver'] . '</td></tr>';
    echo '<tr><td>Host</td><td>' . $config['host'] . '</td></tr>';
    echo '<tr><td>Port</td><td>' . $config['port'] . '</td></tr>';
    echo '<tr><td>Database</td><td>' . $config['database'] . '</td></tr>';
    echo '<tr><td>Username</td><td>' . $config['username'] . '</td></tr>';
    echo '<tr><td>Charset</td><td>' . $config['charset'] . '</td></tr>';
    echo '</table>';
    
    // Veritabanı versiyonu
    $version = $db->select('SELECT VERSION() as version')[0]->version ?? 'Bilinmiyor';
    echo '<div class="info">📊 MySQL Versiyonu: ' . $version . '</div>';
    
} catch (\Exception $e) {
    echo '<div class="error">❌ Veritabanı bağlantı hatası!</div>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    
    echo '<h3>🔧 Olası Çözümler:</h3>';
    echo '<ul>';
    echo '<li>.env dosyasındaki DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD değerlerini kontrol edin</li>';
    echo '<li>Paylaşımlı hosting\'de genellikle DB_HOST = "localhost" olmalı</li>';
    echo '<li>Veritabanı adı genellikle "kullanici_adi_veritabani_adi" formatında olur</li>';
    echo '<li>Hosting panelinden veritabanı bilgilerini kontrol edin</li>';
    echo '</ul>';
}

echo '<h2>3. Mevcut Tablolar</h2>';

try {
    $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
    $tableName = 'Tables_in_' . config('database.connections.mysql.database');
    
    if (count($tables) > 0) {
        echo '<div class="success">✅ ' . count($tables) . ' tablo bulundu</div>';
        echo '<table>';
        echo '<tr><th>Tablo Adı</th></tr>';
        foreach ($tables as $table) {
            $name = $table->$tableName;
            echo '<tr><td>' . $name . '</td></tr>';
        }
        echo '</table>';
        
        // sessions tablosu var mı kontrol et
        $hasSessions = false;
        foreach ($tables as $table) {
            if ($table->$tableName === 'sessions') {
                $hasSessions = true;
                break;
            }
        }
        
        if (!$hasSessions) {
            echo '<div class="error">⚠️ "sessions" tablosu bulunamadı! Migration\'lar çalıştırılmamış.</div>';
        }
    } else {
        echo '<div class="error">⚠️ Veritabanında tablo yok! Migration\'lar çalıştırılmamış.</div>';
    }
    
} catch (\Exception $e) {
    echo '<div class="error">❌ Tablo listesi alınamadı: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

echo '<h2>4. Öneriler</h2>';
echo '<div class="info">';
echo '<strong>Eğer bağlantı başarılıysa ama tablolar yoksa:</strong><br>';
echo '1. Hosting panelinde Terminal/SSH varsa: <code>php artisan migrate</code> çalıştırın<br>';
echo '2. Yoksa, migration dosyalarından SQL oluşturup phpMyAdmin\'den çalıştırın<br>';
echo '3. Veya tarayıcıdan migration çalıştıracak bir script oluşturun (güvenlik riski var)';
echo '</div>';

echo '<div class="error" style="margin-top: 20px;">';
echo '<strong>⚠️ GÜVENLİK UYARISI:</strong><br>';
echo 'Test sonrası bu dosyayı (test-db.php) mutlaka SİLİN!';
echo '</div>';
?>

    </div>
</body>
</html>

