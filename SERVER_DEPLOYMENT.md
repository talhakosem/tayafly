# Sunucuda Blog Görseli Yükleme Sorunu Çözümü

## Sorun
Localde blog görselleri yüklenirken, sunucuda yüklenmiyor.

## Çözüm Adımları

### 1. Sunucu Kontrolü

Tarayıcıdan şu adresi ziyaret edin:
```
https://fidanlik.com.tr/check-server.php
```

Bu script sunucudaki tüm ayarları kontrol edecek.

### 2. SSH ile Bağlanın

Hosting panelinden veya SSH ile sunucuya bağlanın.

### 3. Gerekli Komutlar

```bash
# Proje dizinine gidin
cd /path/to/fidanlik

# Kod güncellemelerini çek (eğer git kullanıyorsanız)
git pull origin main

# Composer bağımlılıklarını güncelle (gerekirse)
composer install --no-dev --optimize-autoloader

# NPM bağımlılıklarını yükle ve production build yap (ÖNEMLİ!)
npm install
npm run build

# Storage link oluştur
php artisan storage:link

# İzinleri düzelt
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 775 storage/app/public/posts
chmod -R 755 public/build

# Cache temizle
php artisan optimize:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear

# Production optimizasyonu
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. .htaccess Kontrolü

`public/.htaccess` dosyasında şu satırların olduğundan emin olun:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Upload boyut limiti
    php_value upload_max_filesize 10M
    php_value post_max_size 10M
    php_value max_execution_time 300
    php_value max_input_time 300
</IfModule>
```

### 5. PHP.ini Ayarları

Hosting panelinizden PHP ayarlarını kontrol edin:

```ini
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 20
max_execution_time = 300
memory_limit = 256M
```

### 6. Nginx Kullanıyorsanız

Nginx config dosyanızda (`/etc/nginx/sites-available/fidanlik.conf`):

```nginx
client_max_body_size 10M;
```

### 7. Log Kontrolü

```bash
# Laravel loglarını kontrol edin
tail -50 storage/logs/laravel.log

# PHP error loglarını kontrol edin
tail -50 /var/log/php-fpm/error.log
# veya
tail -50 /var/log/apache2/error.log
```

### 8. Storage Klasörü Yapısı

Doğru yapı:
```
storage/
  app/
    public/
      posts/
        *.jpg
        *.png

public/
  storage -> ../storage/app/public (symlink)
```

Symlink kontrol:
```bash
ls -la public/ | grep storage
```

### 9. Test

1. Bir blog yazısını düzenleyin
2. Görsel seçin
3. Kaydedin
4. Log dosyasını kontrol edin:
```bash
tail -20 storage/logs/laravel.log
```

## Yaygın Hatalar ve Çözümleri

### Hata 1: "Storage link already exists"
```bash
rm public/storage
php artisan storage:link
```

### Hata 2: "Permission denied"
```bash
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
```

### Hata 3: "413 Request Entity Too Large" (Nginx)
```nginx
# /etc/nginx/nginx.conf
http {
    client_max_body_size 10M;
}

# Sonra restart
sudo systemctl restart nginx
```

### Hata 4: Görsel yüklenmiyor ama hata yok
1. Tarayıcı Network sekmesini kontrol edin (F12)
2. Request payload'da dosyanın olup olmadığına bakın
3. Response'da validation hatası var mı kontrol edin

## Hosting Sağlayıcıya Özel Notlar

### cPanel
1. File Manager -> public_html -> Sağ tık -> Terminal
2. Yukarıdaki komutları çalıştırın

### Plesk
1. PHP Settings -> upload_max_filesize ve post_max_size ayarlayın
2. File Manager -> Console açın
3. Komutları çalıştırın

### DirectAdmin
1. Custom PHP.ini oluşturun
2. Terminal'den komutları çalıştırın

## CSS/JS Dosyalarının Yüklenmemesi Sorunu

### Sorun
Login sayfası veya diğer sayfalarda CSS'ler yüklenmiyor, sayfalar stylesız görünüyor.

### Çözüm
1. `public/hot` dosyasını silin (eğer varsa):
```bash
rm public/hot
```

2. Production build yapın:
```bash
npm install
npm run build
```

3. `public/build` klasörünün var olduğunu ve içinde dosyalar olduğunu kontrol edin:
```bash
ls -la public/build/
ls -la public/build/assets/
```

4. `public/build` klasörünün web server tarafından okunabilir olduğundan emin olun:
```bash
chmod -R 755 public/build
```

5. Tarayıcı cache'ini temizleyin ve sayfayı yenileyin (Ctrl+F5 veya Cmd+Shift+R)

## Kontrol Sonrası

✅ check-server.php dosyasını SİLİN (güvenlik riski)
✅ `public/hot` dosyasını silin (production'da olmamalı)
✅ `public/build` klasörünün var olduğunu kontrol edin
✅ Logları temizleyin
✅ Bir test blog görseli yükleyin
✅ Login sayfasının CSS'lerinin yüklendiğini kontrol edin

## Destek

Sorun devam ediyorsa:
1. `check-server.php` çıktısını kaydedin
2. `storage/logs/laravel.log` son 50 satırını kaydedin
3. Hosting sağlayıcınızın PHP/Apache/Nginx loglarını kontrol edin


