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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('top_link')->default('/');
            $table->string('site_title');
            $table->string('site_description');
            $table->string('email');
            $table->string('phone');
            $table->text('address');
            $table->string('whatsapp')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('google_verification_code')->nullable();
            $table->text('analytics_code')->nullable();
            $table->text('google_map')->nullable();
            $table->text('bank_account_info')->nullable();
            $table->boolean('bank_transfer_enabled')->default(false);
            $table->boolean('cash_on_delivery_card_enabled')->default(false);
            $table->boolean('cash_on_delivery_cash_enabled')->default(false);
            $table->boolean('online_payment_enabled')->default(false);
            $table->string('free_shipping_limit')->nullable();
            $table->string('shipping_cost')->nullable();
            $table->string('discount_threshold')->nullable();
            $table->boolean('discount_type')->default(0);
            $table->string('discount_amount')->nullable();
            $table->string('top_image')->nullable();
            $table->string('order_email')->nullable();
            $table->boolean('credit_card_selection')->default(false);
            $table->string('cash_on_delivery_shipping_cost')->nullable();
            $table->string('top_text')->nullable();
            $table->string('top_text_color')->nullable();
            $table->string('top_background_color')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
