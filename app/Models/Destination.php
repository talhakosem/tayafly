<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'meta_title',
        'meta_description',
        'sort_order',
        'is_active',
        'show_in_menu',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_menu' => 'boolean',
    ];

    /**
     * Destinasyona ait blog yazıları
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_destination')
            ->withTimestamps();
    }

    /**
     * Aktif destinasyonlar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Menüde gösterilecek destinasyonlar
     */
    public function scopeInMenu($query)
    {
        return $query->where('show_in_menu', true);
    }
}

