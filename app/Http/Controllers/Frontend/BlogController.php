<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index(Request $request)
    {
        $query = Post::where('is_published', true)->with('categories');

        // Kategori filtresi
        if ($request->filled('category')) {
            $categoryId = $request->category;
            $query->whereHas('categories', function($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        $posts = $query->with('categories:categories.id,categories.slug')->latest()->paginate(9);

        return view('frontend.blog.index', compact('posts'));
    }

    /**
     * Display the specified blog post.
     * This method handles category slugs and category/blog slug combinations.
     */
    public function show($category_slug, $blog_slug = null)
    {
        // İlk slug kategori olmalı
        $category = \App\Models\Category::where('slug', $category_slug)->first();
        
        if (!$category) {
            abort(404);
        }
        
        // Eğer blog_slug yoksa, kategori sayfasını göster
        if (!$blog_slug) {
            $posts = Post::where('is_published', true)
                ->whereHas('categories', function($q) use ($category) {
                    $q->where('categories.id', $category->id);
                })
                ->with('categories:categories.id,categories.slug')
                ->latest()
                ->paginate(9);
            
            return view('frontend.blog.index', compact('posts', 'category'));
        }
        
        // Blog post kontrolü - kategoriye ait olmalı
        $post = Post::where('slug', $blog_slug)
            ->where('is_published', true)
            ->whereHas('categories', function($q) use ($category) {
                $q->where('categories.id', $category->id);
            })
            ->with('categories:categories.id,categories.slug')
            ->firstOrFail();

        return view('frontend.blog.show', compact('post', 'category'));
    }
}
