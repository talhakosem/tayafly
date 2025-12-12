<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class UpdateAgrolidyaLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-agrolidya-links 
                            {--host=pma.test : E-ticaret veritabanı host adresi}
                            {--database=eticaret : E-ticaret veritabanı adı}
                            {--username=root : Veritabanı kullanıcı adı}
                            {--password= : Veritabanı şifresi}
                            {--port=3306 : Veritabanı portu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'E-ticaret veritabanındaki ürünlerin linklerini fidanlik veritabanındaki ürünlere eşleştirir ve agrolidya_link alanını günceller.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('E-ticaret veritabanından ürün linkleri güncelleniyor...');

        $host = $this->option('host');
        $database = $this->option('database');
        $username = $this->option('username');
        $password = $this->option('password') ?: Config::get('database.connections.mysql.password', env('DB_PASSWORD', ''));
        $port = $this->option('port');

        // E-ticaret veritabanına bağlan
        try {
            $eticaretDb = new \PDO(
                "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]
            );

            $this->info("✓ E-ticaret veritabanına bağlanıldı: {$host}/{$database}");

            // E-ticaret veritabanındaki products tablosundan ürünleri çek
            // Önce tablo yapısını kontrol et
            $tableCheck = $eticaretDb->query("SHOW TABLES LIKE 'products'");
            if ($tableCheck->rowCount() == 0) {
                $this->error("'products' tablosu bulunamadı!");
                return Command::FAILURE;
            }

            // Tablo sütunlarını kontrol et
            $columns = $eticaretDb->query("SHOW COLUMNS FROM products")->fetchAll();
            $columnNames = array_column($columns, 'Field');
            
            $this->info("Tablo sütunları: " . implode(', ', $columnNames));

            // Slug veya name alanını bul
            $slugColumn = null;
            $nameColumn = null;
            
            if (in_array('slug', $columnNames)) {
                $slugColumn = 'slug';
            } elseif (in_array('sef', $columnNames)) {
                $slugColumn = 'sef';
            }
            
            if (in_array('name', $columnNames)) {
                $nameColumn = 'name';
            } elseif (in_array('baslik', $columnNames)) {
                $nameColumn = 'baslik';
            } elseif (in_array('title', $columnNames)) {
                $nameColumn = 'title';
            }

            if (!$slugColumn && !$nameColumn) {
                $this->error("Slug veya name alanı bulunamadı! Mevcut sütunlar: " . implode(', ', $columnNames));
                return Command::FAILURE;
            }

            // Ürünleri çek
            $selectColumns = ['id'];
            if ($slugColumn) {
                $selectColumns[] = $slugColumn;
            }
            if ($nameColumn) {
                $selectColumns[] = $nameColumn;
            }

            $query = "SELECT " . implode(', ', $selectColumns) . " FROM products";
            $stmt = $eticaretDb->query($query);
            $eticaretProducts = $stmt->fetchAll();

            $this->info("E-ticaret veritabanında " . count($eticaretProducts) . " ürün bulundu.");

            // Fidanlik veritabanındaki tüm ürünleri çek
            $fidanlikProducts = Product::all();
            $this->info("Fidanlik veritabanında " . $fidanlikProducts->count() . " ürün bulundu.");

            $updated = 0;
            $notFound = 0;
            $skipped = 0;

            // Eşleştirme yap
            foreach ($fidanlikProducts as $fidanlikProduct) {
                $matched = false;
                $matchedSlug = null;

                // Önce slug'a göre eşleştir
                if ($slugColumn) {
                    foreach ($eticaretProducts as $eticaretProduct) {
                        if (!empty($eticaretProduct[$slugColumn]) && 
                            strtolower(trim($eticaretProduct[$slugColumn])) === strtolower(trim($fidanlikProduct->slug))) {
                            $matchedSlug = $eticaretProduct[$slugColumn];
                            $matched = true;
                            break;
                        }
                    }
                }

                // Slug ile eşleşmediyse, name'e göre eşleştir
                if (!$matched && $nameColumn) {
                    foreach ($eticaretProducts as $eticaretProduct) {
                        if (!empty($eticaretProduct[$nameColumn]) && 
                            strtolower(trim($eticaretProduct[$nameColumn])) === strtolower(trim($fidanlikProduct->name))) {
                            // Name'e göre eşleşti, slug'ı kullan veya name'den slug oluştur
                            if ($slugColumn && !empty($eticaretProduct[$slugColumn])) {
                                $matchedSlug = $eticaretProduct[$slugColumn];
                            } else {
                                // Slug yoksa name'den slug oluştur
                                $matchedSlug = \Illuminate\Support\Str::slug($eticaretProduct[$nameColumn]);
                            }
                            $matched = true;
                            break;
                        }
                    }
                }

                if ($matched && $matchedSlug) {
                    // Link oluştur: agrolidya.com/slug
                    $agrolidyaLink = 'https://agrolidya.com/' . $matchedSlug;
                    
                    // Eğer zaten aynı link varsa atla
                    if ($fidanlikProduct->agrolidya_link === $agrolidyaLink) {
                        $skipped++;
                        continue;
                    }

                    $fidanlikProduct->agrolidya_link = $agrolidyaLink;
                    $fidanlikProduct->save();
                    
                    $this->info("✓ ID {$fidanlikProduct->id}: '{$fidanlikProduct->name}' -> {$agrolidyaLink}");
                    $updated++;
                } else {
                    $this->warn("✗ ID {$fidanlikProduct->id}: '{$fidanlikProduct->name}' eşleşme bulunamadı");
                    $notFound++;
                }
            }

            $this->info("\nİşlem tamamlandı!");
            $this->info("Güncellenen: {$updated}");
            $this->info("Eşleşme bulunamayan: {$notFound}");
            $this->info("Zaten güncel olan: {$skipped}");

            return Command::SUCCESS;

        } catch (\PDOException $e) {
            $this->error("Veritabanı bağlantı hatası: " . $e->getMessage());
            $this->error("Lütfen bağlantı bilgilerini kontrol edin.");
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error("Hata: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

