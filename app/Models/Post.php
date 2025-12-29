<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'slug',
        'content',
        'user_id',
        'cover_image',
        'short_description',
        'category',
        'tags',
        'meta_title',
        'meta_description',
        'is_published',
        'comments_enabled',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
        'comments_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Categories relationship (many-to-many)
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'post_category');
    }

    // Destinations relationship (many-to-many)
    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'post_destination')
            ->withTimestamps();
    }

    /**
     * Get the URL for this post.
     * If post has a destination, use destination URL, otherwise use category URL.
     */
    public function getUrl(): string
    {
        // Önce destination kontrolü - destination varsa öncelikli
        $destination = $this->destinations->first();
        if ($destination) {
            // /private-jet-charter-london/blog-slug şeklinde
            return url('/' . $destination->slug . '/' . $this->slug);
        }

        // Destination yoksa kategori URL'i kullan
        $category = $this->categories->first();
        if ($category) {
            return url('/' . $category->slug . '/' . $this->slug);
        }

        // Hiçbiri yoksa sadece blog slug'ı
        return url('/blog/' . $this->slug);
    }

    /**
     * Get URL attribute for easy access in views.
     */
    public function getUrlAttribute(): string
    {
        return $this->getUrl();
    }
}