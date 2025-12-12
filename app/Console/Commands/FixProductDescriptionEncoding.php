<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class FixProductDescriptionEncoding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-product-description-encoding {--force : Perform the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes Turkish character encoding issues in product descriptions by converting HTML entities to proper characters.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Ürün açıklamalarındaki Türkçe karakter sorunları düzeltiliyor...');

        $products = Product::whereNotNull('description')->get();
        $this->info("Toplam " . $products->count() . " ürün bulundu.");

        $fixedCount = 0;
        $unchangedCount = 0;

        foreach ($products as $product) {
            $originalDescription = $product->description;
            
            // HTML entity'leri decode et
            $decodedDescription = html_entity_decode($originalDescription, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            // Eğer değişiklik varsa güncelle
            if ($decodedDescription !== $originalDescription) {
                $product->description = $decodedDescription;
                $product->save();
                
                $this->info("✓ ID {$product->id}: Düzeltildi");
                $fixedCount++;
            } else {
                $unchangedCount++;
            }
        }

        $this->info("\nİşlem tamamlandı!");
        $this->info("Düzeltilen: {$fixedCount}");
        $this->info("Değişmeyen: {$unchangedCount}");

        return Command::SUCCESS;
    }
}
