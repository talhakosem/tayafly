<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        // Random blog yazıları getir
        $randomPosts = \App\Models\Post::where('is_published', true)
            ->with('categories:categories.id,categories.slug')
            ->inRandomOrder()
            ->limit(6)
            ->get();
        
        return view('frontend.home', compact('randomPosts'));
    }

    /**
     * Display the about page.
     */
    public function about()
    {
        return view('frontend.about');
    }
}
