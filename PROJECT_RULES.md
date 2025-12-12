# 🎯 Proje Kuralları ve Geliştirme Rehberi

## Amaç

Bu proje, Laravel 12 tabanlı, temiz kodlu ve sürdürülebilir bir web yapısı oluşturmak içindir.

Tema hazır olacak, biz sadece verileri dinamik hale getireceğiz.

## ⚙️ Genel Kurallar

### 1. Framework
- **Laravel 12** kullanılacak.

### 2. Kod Stili
- **PSR-12** standardına uy.
- Gereksiz satır, import veya boşluk ekleme.
- Açıklayıcı method isimleri kullan.

### 3. Dosya Yapısı
- `app/Models` içinde modeller
- `app/Http/Controllers` içinde controllerlar
- `resources/views` içinde blade dosyaları olacak.

### 4. Bağlantılar
- Controller → Model → View zinciri dışında direkt DB sorgusu yapılmayacak.

### 5. Migration
- Yeni tablolar oluşturulurken Laravel migration yapısı kullanılacak.

### 6. Auth
- Login / Register gibi işlemlerde **Laravel Breeze** kullanılacak.

### 7. Yorumlama
- Yazdığın her kod bloğunun sonunda kısa bir açıklama yaz.
- **Örnek:** "Bu kısımda blog verilerini slug'a göre çekiyoruz çünkü URL'de SEO dostu yapı kullanıyoruz."

### 8. Frontend Uyumu
- Temadaki HTML yapısına bağlı kal.
- Sadece gerekli kısımları dinamikleştir.
- Blade yapısına uygun şekilde değiştir (`@foreach`, `@include`, `@yield`, `@extends`).

### 9. Performans
- Gereksiz DB çağrısı yapma.
- Gerektiğinde `with()` ile ilişkileri eager load et.

### 10. Açıklama Stili
- Karmaşık bir işlem yaptıysan, önce bir paragrafla bana "neden" yaptığını açıkla.
- **Örnek:** "Bunu bu şekilde yaptım çünkü Laravel'de bu mantık X durumunu kolaylaştırır."

## 📦 Kullanılabilecek Paketler

- `laravel/breeze` → Auth sistemi
- `spatie/laravel-sitemap` → SEO sitemap
- `intervention/image` → Görsel işlemleri
- `barryvdh/laravel-debugbar` → Debug aracı

## 🧱 Çalışma Biçimi

1. Her adımı bölüm bölüm yapacağız.
2. Her bölümün sonunda sen bana kısa bir özet vereceksin: "Bu adımda şunu yaptık, nedeni şu."
3. Gereksiz dosya oluşturma; her şeyin yeri doğru olsun.
4. Gerektiğinde Controller veya Route içinde kısa `TODO:` notları bırakabilirsin.

## 🧩 Örnek

Eğer blog modülü oluşturacaksak:
- Migration → Model → Controller → Route → Blade sıralamasını takip et.
- Koddan sonra 1 paragraf açıklama yaz:

> "Bu yapıyı MVC'ye göre kurduk çünkü Laravel'de verinin akışı bu sırayı izler. Böylece kod okunabilirliği ve bakımı kolaylaşır."

## 🔍 Hedef

Bu sistem sadece çalışsın değil, öğretici de olsun.

Her işlemde nedenini açıkla, Laravel'in mantığını öğret, ama kodu profesyonel şekilde yaz.

---

**Son Güncelleme:** 2025-01-27


