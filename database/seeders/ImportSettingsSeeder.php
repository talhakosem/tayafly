<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class ImportSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mevcut veritabanı bağlantı bilgilerini al
        $host = Config::get('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
        $port = Config::get('database.connections.mysql.port', env('DB_PORT', '3306'));
        $username = Config::get('database.connections.mysql.username', env('DB_USERNAME', 'root'));
        $password = Config::get('database.connections.mysql.password', env('DB_PASSWORD', ''));
        
        // Fidanlik veritabanına bağlan
        try {
            $fidanlikDb = new \PDO(
                "mysql:host={$host};port={$port};dbname=fidanlik;charset=utf8mb4",
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]
            );
            
            // Ayarlar tablosundan verileri çek
            $stmt = $fidanlikDb->query("SELECT * FROM ayarlar LIMIT 1");
            $oldSettings = $stmt->fetch();
            
            if (!$oldSettings) {
                $this->command->warn("Ayarlar tablosunda veri bulunamadı.");
                return;
            }
            
            // Ayarları oluştur veya güncelle
            $setting = Setting::firstOrNew(['id' => 1]);
            
            $setting->logo = $oldSettings['logo'] ?? null;
            $setting->favicon = $oldSettings['fav'] ?? null;
            $setting->top_link = $oldSettings['site_ust_link'] ?? '/';
            $setting->site_title = $oldSettings['title'] ?? '';
            $setting->site_description = $oldSettings['description'] ?? '';
            $setting->email = $oldSettings['email'] ?? '';
            $setting->phone = $oldSettings['telefon'] ?? '';
            $setting->address = $oldSettings['adres'] ?? '';
            $setting->whatsapp = $oldSettings['whatsapp'] ?? null;
            $setting->facebook_url = $oldSettings['facebook'] ?? null;
            $setting->twitter_url = $oldSettings['twitter'] ?? null;
            $setting->instagram_url = $oldSettings['instagram'] ?? null;
            $setting->youtube_url = $oldSettings['youtube'] ?? null;
            $setting->google_verification_code = $oldSettings['google_dogrulama_kodu'] ?? null;
            $setting->analytics_code = $oldSettings['analytics'] ?? null;
            $setting->google_map = $oldSettings['google_harita'] ?? null;
            $setting->bank_account_info = $oldSettings['iban_bilgileri'] ?? null;
            $setting->bank_transfer_enabled = (bool)($oldSettings['banka_havalesi'] ?? 0);
            $setting->cash_on_delivery_card_enabled = (bool)($oldSettings['kapida_kredi_karti'] ?? 0);
            $setting->cash_on_delivery_cash_enabled = (bool)($oldSettings['kapida_nakit'] ?? 0);
            $setting->online_payment_enabled = (bool)($oldSettings['online_odeme'] ?? 0);
            $setting->free_shipping_limit = $oldSettings['kargo_bedava_limit'] ?? null;
            $setting->shipping_cost = $oldSettings['kargo_ucreti'] ?? null;
            $setting->discount_threshold = $oldSettings['kac_lira_uzeri_indirim'] ?? null;
            $setting->discount_type = (bool)($oldSettings['kac_lira_uzeri_indirim_turu'] ?? 0);
            $setting->discount_amount = $oldSettings['kac_lira_uzeri_indirim_tutari'] ?? null;
            $setting->top_image = $oldSettings['site_ust_img'] ?? null;
            $setting->order_email = $oldSettings['siparis_email'] ?? null;
            $setting->credit_card_selection = (bool)($oldSettings['kredi_karti_secim'] ?? 0);
            $setting->cash_on_delivery_shipping_cost = $oldSettings['kapida_odeme_kargo_ucreti'] ?? null;
            $setting->top_text = $oldSettings['site_ust_yazi'] ?? null;
            $setting->top_text_color = $oldSettings['site_ust_yazi_renk'] ?? null;
            $setting->top_background_color = $oldSettings['site_ust_arka_renk'] ?? null;
            
            $setting->save();
            
            $this->command->info("✓ Ayarlar başarıyla aktarıldı.");
            
        } catch (\PDOException $e) {
            $this->command->error("Veritabanı bağlantı hatası: " . $e->getMessage());
            $this->command->error("Lütfen .env dosyanızdaki DB_HOST, DB_USERNAME, DB_PASSWORD bilgilerinin doğru olduğundan emin olun.");
        } catch (\Exception $e) {
            $this->command->error("Hata: " . $e->getMessage());
        }
    }
}
