<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'description',
        'agrolidya_link',
        'meta_description',
        'meta_title',
        'min_quantity',
        'stock',
        'sku',
        'category_id',
        'brand_id',
        'delivery_date',
        'is_active',
    ];

    protected $casts = [
        'min_quantity' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'delivery_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }
}
