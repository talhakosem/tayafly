<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Foreign key constraint'leri nedeniyle sırayla kaldırıyoruz
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product_category');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback durumunda tabloları yeniden oluşturmak için
        // orijinal migration dosyalarını kullanabilirsiniz
    }
};
