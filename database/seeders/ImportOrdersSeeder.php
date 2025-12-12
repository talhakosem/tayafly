<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class ImportOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $host = Config::get('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
        $port = Config::get('database.connections.mysql.port', env('DB_PORT', '3306'));
        $username = Config::get('database.connections.mysql.username', env('DB_USERNAME', 'root'));
        $password = Config::get('database.connections.mysql.password', env('DB_PASSWORD', ''));

        try {
            $fidanlikDb = new \PDO(
                "mysql:host={$host};port={$port};dbname=fidanlik;charset=utf8mb4",
                $username,
                $password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]
            );

            // Siparişleri çek
            $stmt = $fidanlikDb->query("SELECT * FROM siparis ORDER BY id");
            $orders = $stmt->fetchAll();

            $this->command->info("Toplam " . count($orders) . " sipariş bulundu.");

            $imported = 0;
            $skipped = 0;

            foreach ($orders as $orderData) {
                // Aynı ID'ye sahip sipariş varsa atla
                if (Order::where('id', $orderData['id'])->exists()) {
                    $skipped++;
                    continue;
                }

                Order::create([
                    'id' => $orderData['id'],
                    'customer_name' => $orderData['adisoyadi'] ?? '',
                    'email' => $orderData['email'] ?? '',
                    'phone' => $orderData['telefon'] ?? '',
                    'address' => $orderData['adres'] ?? null,
                    'order_date' => date('Y-m-d H:i:s', $orderData['siparis_tarihi']),
                    'total_amount' => $orderData['toplam_tutar'] ?? 0,
                    'order_key' => $orderData['siparis_key'] ?? '',
                    'credit_card_paid' => (bool)($orderData['kredi_karti_odendi'] ?? false),
                    'status' => $orderData['durum'] ?? 0,
                    'user_id' => $orderData['kullanici_id'] > 0 ? $orderData['kullanici_id'] : null,
                    'payment_method' => $orderData['odeme_yontemi'] ?? 0,
                    'tc_no' => $orderData['tc'] ?? null,
                    'shipping_cost' => $orderData['kargo'] ?? 0,
                    'service_fee' => $orderData['hizmet'] ?? 0,
                    'discount' => $orderData['indirim'] ?? 0,
                    'coupon_code' => $orderData['kupon'] ?? null,
                    'invoice_type' => $orderData['ftipi'] ?? null,
                    'invoice_title' => $orderData['faturaunvan'] ?? null,
                    'invoice_tax_office' => $orderData['faturavd'] ?? null,
                    'invoice_tax_no' => $orderData['faturavn'] ?? null,
                    'invoice_address' => $orderData['faturaadres'] ?? null,
                    'invoice_city' => $orderData['faturasehir'] ?? null,
                    'invoice_district' => $orderData['faturailce'] ?? null,
                    'shipping_name' => $orderData['teskisi'] ?? null,
                    'shipping_address' => $orderData['tesadres'] ?? null,
                    'shipping_city' => $orderData['tessehir'] ?? null,
                    'shipping_district' => $orderData['tesilce'] ?? null,
                    'mail_sent' => $orderData['mailt'] ?? null,
                    'invoice_no' => $orderData['faturano'] ?? null,
                    'created_at' => date('Y-m-d H:i:s', $orderData['siparis_tarihi']),
                    'updated_at' => date('Y-m-d H:i:s', $orderData['siparis_tarihi']),
                ]);

                $imported++;
                $this->command->info("✓ Sipariş ID {$orderData['id']} aktarıldı");
            }

            $this->command->info("\nSipariş aktarımı tamamlandı!");
            $this->command->info("Aktarılan: {$imported}");
            $this->command->info("Atlanan: {$skipped}");

            // Şimdi sipariş ürünlerini aktar
            $this->command->info("\n\nSipariş ürünleri aktarılıyor...");

            $stmt = $fidanlikDb->query("SELECT * FROM siparis_urun ORDER BY id");
            $orderItems = $stmt->fetchAll();

            $this->command->info("Toplam " . count($orderItems) . " sipariş ürünü bulundu.");

            $itemImported = 0;
            $itemSkipped = 0;

            foreach ($orderItems as $itemData) {
                // Sipariş var mı kontrol et
                if (!Order::where('id', $itemData['siparis_id'])->exists()) {
                    $itemSkipped++;
                    continue;
                }

                // Aynı ID varsa atla
                if (OrderItem::where('id', $itemData['id'])->exists()) {
                    $itemSkipped++;
                    continue;
                }

                OrderItem::create([
                    'id' => $itemData['id'],
                    'order_id' => $itemData['siparis_id'],
                    'product_id' => $itemData['urun_id'] > 0 ? $itemData['urun_id'] : null,
                    'price' => $itemData['fiyat'] ?? 0,
                    'quantity' => $itemData['adet'] ?? 1,
                    'option_price' => $itemData['secenektt'] ?? null,
                    'option_id' => $itemData['secenek_id'] ?? null,
                    'option_text' => $itemData['secenek'] ?? null,
                    'option1' => $itemData['sec1'] ?? null,
                    'option2' => $itemData['sec2'] ?? null,
                ]);

                $itemImported++;
            }

            $this->command->info("\nSipariş ürün aktarımı tamamlandı!");
            $this->command->info("Aktarılan: {$itemImported}");
            $this->command->info("Atlanan: {$itemSkipped}");

        } catch (\PDOException $e) {
            $this->command->error("Veritabanı bağlantı hatası: " . $e->getMessage());
        } catch (\Exception $e) {
            $this->command->error("Hata: " . $e->getMessage());
        }
    }
}
