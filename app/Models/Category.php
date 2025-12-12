<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'description',
        'meta_description',
        'meta_title',
        'image',
        'icon',
        'parent_id',
        'sort_order',
        'show_in_top_menu',
        'show_in_footer_menu',
    ];

    protected $casts = [
        'show_in_top_menu' => 'boolean',
        'show_in_footer_menu' => 'boolean',
        'parent_id' => 'integer',
        'sort_order' => 'integer',
    ];

    // Parent category relationship
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Child categories relationship
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Posts relationship (many-to-many)
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_category');
    }
}
