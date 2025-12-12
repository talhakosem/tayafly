<?php
// Storage link kontrolü
echo "Storage Link Kontrolü<br><br>";

$publicStorage = __DIR__ . '/storage';
echo "Public storage path: $publicStorage<br>";

if (file_exists($publicStorage)) {
    if (is_link($publicStorage)) {
        echo "✓ Symlink VAR<br>";
        echo "Link hedefi: " . readlink($publicStorage) . "<br>";
    } else {
        echo "❌ Dizin var ama SYMLINK DEĞİL!<br>";
        echo "Çözüm: rm -rf $publicStorage && php artisan storage:link<br>";
    }
} else {
    echo "❌ Storage link/dizini YOK!<br>";
    echo "Çözüm: php artisan storage:link<br>";
}

echo "<br>Proje yolu: " . dirname(__DIR__) . "<br>";
?>


