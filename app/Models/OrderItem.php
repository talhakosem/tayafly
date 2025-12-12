<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'price',
        'quantity',
        'option_price',
        'option_id',
        'option_text',
        'option1',
        'option2',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'option_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->price * $this->quantity;
    }
}
