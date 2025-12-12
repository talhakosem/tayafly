<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'favicon',
        'top_link',
        'site_title',
        'site_description',
        'email',
        'phone',
        'address',
        'whatsapp',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'youtube_url',
        'google_verification_code',
        'analytics_code',
        'google_map',
        'bank_account_info',
        'bank_transfer_enabled',
        'cash_on_delivery_card_enabled',
        'cash_on_delivery_cash_enabled',
        'online_payment_enabled',
        'free_shipping_limit',
        'shipping_cost',
        'discount_threshold',
        'discount_type',
        'discount_amount',
        'top_image',
        'order_email',
        'credit_card_selection',
        'cash_on_delivery_shipping_cost',
        'top_text',
        'top_text_color',
        'top_background_color',
    ];

    protected $casts = [
        'bank_transfer_enabled' => 'boolean',
        'cash_on_delivery_card_enabled' => 'boolean',
        'cash_on_delivery_cash_enabled' => 'boolean',
        'online_payment_enabled' => 'boolean',
        'credit_card_selection' => 'boolean',
        'discount_type' => 'boolean',
    ];

    /**
     * Get the first (and only) settings record
     */
    public static function getSettings()
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
