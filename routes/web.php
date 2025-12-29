<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;


Route::get('/', [\App\Http\Controllers\Frontend\HomeController::class, 'index'])->name('home');

// Frontend Blog Routes
Route::get('blog', [\App\Http\Controllers\Frontend\BlogController::class, 'index'])->name('frontend.blog.index');

// Frontend Static Pages
Route::get('hakkimizda', [\App\Http\Controllers\Frontend\HomeController::class, 'about'])->name('frontend.about');

// Frontend Destination Routes
Route::get('destination/{slug}', [\App\Http\Controllers\Frontend\DestinationController::class, 'show'])->name('destination.show');

// Sitemap
Route::get('sitemap.xml', [\App\Http\Controllers\Frontend\SitemapController::class, 'index'])->name('sitemap');
Route::get('sitemap/compare', [\App\Http\Controllers\Frontend\SitemapController::class, 'compare'])->middleware('auth')->name('sitemap.compare');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::resource('posts', PostController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('destinations', \App\Http\Controllers\DestinationController::class);
    Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';

// Universal slug route - must be last to avoid conflicts
// This route handles: categories and blog posts with category prefix
Route::get('{category_slug}/{blog_slug?}', [\App\Http\Controllers\Frontend\BlogController::class, 'show'])->name('frontend.slug');
