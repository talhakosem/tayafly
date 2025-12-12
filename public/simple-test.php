<?php
// Basit test
echo "PHP çalışıyor!<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Storage klasörü kontrolü
$storagePath = __DIR__ . '/../storage/app/public/posts';
echo "<br>Storage Path: $storagePath<br>";
echo "Klasör var mı: " . (is_dir($storagePath) ? 'EVET' : 'HAYIR') . "<br>";

if (is_dir($storagePath)) {
    echo "Yazılabilir mi: " . (is_writable($storagePath) ? 'EVET' : 'HAYIR') . "<br>";
    echo "İzinler: " . substr(sprintf('%o', fileperms($storagePath)), -4) . "<br>";
}

// PHP extensions
echo "<br>fileinfo extension: " . (extension_loaded('fileinfo') ? 'VAR' : 'YOK') . "<br>";
echo "gd extension: " . (extension_loaded('gd') ? 'VAR' : 'YOK') . "<br>";

// Upload ayarları
echo "<br>upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "file_uploads: " . (ini_get('file_uploads') ? 'AÇIK' : 'KAPALI') . "<br>";
?>


