<?php
/**
 * Migration Çalıştırma Scripti
 * Kullanım: https://tayafly.com/run-migrations.php
 * 
 * ÖNEMLİ: Bu scripti çalıştırdıktan sonra SİLİN!
 */

// Laravel bootstrap
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Migration Çalıştır</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: green; background: #d4edda; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 4px; margin: 10px 0; }
        pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Migration Çalıştır</h1>

<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

// Veritabanı bağlantısı kontrol
echo '<h2>1. Veritabanı Bağlantısı</h2>';

try {
    DB::connection()->getPdo();
    echo '<div class="success">✅ Veritabanı bağlantısı başarılı!</div>';
    
    $dbName = DB::connection()->getDatabaseName();
    echo '<div class="info">Veritabanı: <strong>' . $dbName . '</strong></div>';
    
} catch (\Exception $e) {
    echo '<div class="error">❌ Veritabanı bağlantısı başarısız!</div>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<div class="warning">⚠️ .env dosyasındaki veritabanı ayarlarını kontrol edin.</div>';
    exit;
}

// Migration işlemi
if (isset($_POST['run_migrations'])) {
    echo '<h2>2. Migration Çalıştırılıyor...</h2>';
    
    try {
        // Artisan migrate komutunu çalıştır
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();
        
        echo '<div class="success">✅ Migration başarıyla çalıştırıldı!</div>';
        echo '<pre>' . htmlspecialchars($output) . '</pre>';
        
    } catch (\Exception $e) {
        echo '<div class="error">❌ Migration hatası!</div>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    }
}

// Mevcut tabloları listele
echo '<h2>3. Mevcut Tablolar</h2>';

try {
    $tables = DB::select('SHOW TABLES');
    $tableName = 'Tables_in_' . DB::connection()->getDatabaseName();
    
    if (count($tables) > 0) {
        echo '<div class="success">✅ ' . count($tables) . ' tablo mevcut</div>';
        echo '<ul>';
        foreach ($tables as $table) {
            echo '<li>' . $table->$tableName . '</li>';
        }
        echo '</ul>';
        
        // Önemli tabloları kontrol et
        $requiredTables = ['users', 'sessions', 'categories', 'posts', 'settings', 'post_category'];
        $missingTables = [];
        
        $existingTables = array_map(function($t) use ($tableName) {
            return $t->$tableName;
        }, $tables);
        
        foreach ($requiredTables as $req) {
            if (!in_array($req, $existingTables)) {
                $missingTables[] = $req;
            }
        }
        
        if (count($missingTables) > 0) {
            echo '<div class="warning">⚠️ Eksik tablolar: ' . implode(', ', $missingTables) . '</div>';
            echo '<div class="info">Migration çalıştırarak eksik tabloları oluşturabilirsiniz.</div>';
        } else {
            echo '<div class="success">✅ Tüm gerekli tablolar mevcut!</div>';
        }
    } else {
        echo '<div class="warning">⚠️ Veritabanında hiç tablo yok!</div>';
        echo '<div class="info">Migration çalıştırarak tabloları oluşturabilirsiniz.</div>';
    }
    
} catch (\Exception $e) {
    echo '<div class="error">❌ Tablo listesi alınamadı: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

// Çalıştır butonu
if (!isset($_POST['run_migrations'])) {
    ?>
    <h2>4. Migration Çalıştır</h2>
    <div class="warning">
        <strong>⚠️ DİKKAT:</strong> Bu işlem veritabanınızda tablolar oluşturacak veya güncelleyecektir.
    </div>
    <form method="POST" style="margin: 20px 0;">
        <button type="submit" name="run_migrations" value="1" class="btn">
            🚀 Migration Çalıştır
        </button>
    </form>
    <?php
}

echo '<div class="error" style="margin-top: 30px;">';
echo '<strong>⚠️ GÜVENLİK UYARISI:</strong><br>';
echo 'Migration başarılı olduktan sonra bu dosyayı (<code>run-migrations.php</code>) mutlaka SİLİN!';
echo '</div>';
?>

    </div>
</body>
</html>

