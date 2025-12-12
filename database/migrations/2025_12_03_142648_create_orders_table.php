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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('email');
            $table->string('phone');
            $table->text('address')->nullable();
            $table->timestamp('order_date');
            $table->decimal('total_amount', 18, 2);
            $table->string('order_key')->unique();
            $table->boolean('credit_card_paid')->default(false);
            $table->integer('status')->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('payment_method')->default(0);
            $table->string('tc_no')->nullable();
            $table->decimal('shipping_cost', 18, 2)->default(0);
            $table->decimal('service_fee', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->string('coupon_code')->nullable();
            
            // Fatura bilgileri
            $table->string('invoice_type')->nullable();
            $table->string('invoice_title')->nullable();
            $table->string('invoice_tax_office')->nullable();
            $table->string('invoice_tax_no')->nullable();
            $table->text('invoice_address')->nullable();
            $table->string('invoice_city')->nullable();
            $table->string('invoice_district')->nullable();
            
            // Teslimat bilgileri
            $table->string('shipping_name')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_district')->nullable();
            
            // Diğer
            $table->text('mail_sent')->nullable();
            $table->string('invoice_no')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('order_key');
            $table->index('email');
            $table->index('status');
            $table->index('order_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
