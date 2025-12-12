<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'customer_name',
        'email',
        'phone',
        'address',
        'order_date',
        'total_amount',
        'order_key',
        'credit_card_paid',
        'status',
        'user_id',
        'payment_method',
        'tc_no',
        'shipping_cost',
        'service_fee',
        'discount',
        'coupon_code',
        'invoice_type',
        'invoice_title',
        'invoice_tax_office',
        'invoice_tax_no',
        'invoice_address',
        'invoice_city',
        'invoice_district',
        'shipping_name',
        'shipping_address',
        'shipping_city',
        'shipping_district',
        'mail_sent',
        'invoice_no',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'credit_card_paid' => 'boolean',
        'status' => 'integer',
        'payment_method' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            0 => 'Beklemede',
            1 => 'Onaylandı',
            2 => 'Hazırlanıyor',
            3 => 'Kargoya Verildi',
            4 => 'Teslim Edildi',
            5 => 'İptal Edildi',
        ];

        return $statuses[$this->status] ?? 'Bilinmiyor';
    }

    public function getPaymentMethodTextAttribute()
    {
        $methods = [
            0 => 'Belirtilmemiş',
            1 => 'Kredi Kartı',
            2 => 'Havale/EFT',
            3 => 'Kapıda Ödeme',
            4 => 'Kapıda Kredi Kartı',
        ];

        return $methods[$this->payment_method] ?? 'Bilinmiyor';
    }
}
