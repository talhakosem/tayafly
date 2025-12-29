<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\StorageHelper;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('categories')->latest()->paginate(10);

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $destinations = Destination::active()->orderBy('name')->get();
        return view('posts.create', compact('categories', 'destinations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'short_description' => 'nullable|string',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'destination_ids' => 'nullable|array',
            'destination_ids.*' => 'exists:destinations,id',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ], [
            'category_id.required' => 'Önce kategori seçmelisin.',
            'category_id.exists' => 'Seçilen kategori geçerli değil.',
        ]);

        // Destination ID'leri ayır
        $destinationIds = $data['destination_ids'] ?? [];
        unset($data['destination_ids']);

        // Görsel yükleme
        if ($request->hasFile('cover_image')) {
            try {
                $uploadedPath = StorageHelper::storeAndCopy($request->file('cover_image'), 'posts');
                if ($uploadedPath) {
                    $data['cover_image'] = $uploadedPath;
                } else {
                    Log::warning('Blog görseli yüklenemedi (store)');
                }
            } catch (\Exception $e) {
                Log::error('Blog görsel yükleme hatası (store)', ['error' => $e->getMessage()]);
            }
        }

        // Tags'i array'e çevir
        if (isset($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        $data['slug'] = Str::slug($data['title']);
        $data['user_id'] = Auth::id();
        $data['is_published'] = $request->has('is_published');
        $data['comments_enabled'] = false; // Yorumlar kapalı

        // Kategoriyi ayır
        $categoryId = $data['category_id'] ?? null;
        unset($data['category_id']);

        $post = Post::create($data);
        
        // Kategoriyi attach et
        if ($categoryId) {
            $post->categories()->sync([$categoryId]);
        }

        // Destinasyonları attach et
        if (!empty($destinationIds)) {
            $post->destinations()->sync($destinationIds);
        }

        return redirect()->route('posts.index')
            ->with('success', 'Blog yazısı başarıyla oluşturuldu.');
    }

    public function edit(Post $post)
    {
        $categories = Category::orderBy('name')->get();
        $destinations = Destination::active()->orderBy('name')->get();
        $post->load(['categories', 'destinations']);
        return view('posts.edit', compact('post', 'categories', 'destinations'));
    }

    public function update(Request $request, Post $post)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'short_description' => 'nullable|string',
                'content' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'destination_ids' => 'nullable|array',
                'destination_ids.*' => 'exists:destinations,id',
                'tags' => 'nullable|string',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'is_published' => 'nullable|boolean',
            ], [
                'title.required' => 'Başlık alanı zorunludur.',
                'cover_image.image' => 'Kapak görseli geçerli bir resim dosyası olmalıdır.',
                'cover_image.mimes' => 'Kapak görseli jpeg, png, jpg, gif veya webp formatında olmalıdır.',
                'cover_image.max' => 'Kapak görseli maksimum 5MB olabilir.',
                'content.required' => 'İçerik alanı zorunludur.',
                'category_id.required' => 'Önce kategori seçmelisin.',
                'category_id.exists' => 'Seçilen kategori geçerli değil.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Blog güncelleme validation hatası', [
                'post_id' => $post->id,
                'errors' => $e->errors()
            ]);
            throw $e;
        }

        // Görsel yükleme - BASITLEŞTIRILDI ve DEBUG eklendi
        if ($request->hasFile('cover_image')) {
            Log::info('Blog görseli yükleniyor', [
                'post_id' => $post->id,
                'file_name' => $request->file('cover_image')->getClientOriginalName(),
                'file_size' => $request->file('cover_image')->getSize(),
                'mime_type' => $request->file('cover_image')->getMimeType()
            ]);
            
            try {
                $file = $request->file('cover_image');
                
                    // Dosya geçerli mi kontrol et
                if ($file->isValid()) {
                    // Eski görseli sil
                    if ($post->cover_image) {
                        StorageHelper::deleteFromBoth($post->cover_image);
                        Log::info('Eski görsel silindi', ['path' => $post->cover_image]);
                    }
                    
                    // Yeni görseli yükle ve kopyala
                    $uploadedPath = StorageHelper::storeAndCopy($file, 'posts');
                    
                    if ($uploadedPath) {
                        $data['cover_image'] = $uploadedPath;
                        Log::info('Görsel başarıyla yüklendi ve kopyalandı', ['path' => $uploadedPath]);
                    } else {
                        Log::error('Görsel yüklenemedi - store false döndü');
                    }
                } else {
                    Log::error('Geçersiz dosya', ['error' => $file->getError()]);
                }
            } catch (\Exception $e) {
                Log::error('Blog görsel yükleme exception', [
                    'post_id' => $post->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } else {
            Log::info('Görsel yüklenmedi', [
                'post_id' => $post->id,
                'has_file' => $request->hasFile('cover_image'),
                'all_files' => $request->allFiles()
            ]);
        }

        // Tags'i array'e çevir
        if (isset($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        // Kategoriyi ayır
        $categoryId = $data['category_id'] ?? null;
        unset($data['category_id']);

        // Destination ID'leri ayır
        $destinationIds = $data['destination_ids'] ?? [];
        unset($data['destination_ids']);

        // Slug'ı değiştirme (SEO için mevcut slug'ı koru)
        // $data['slug'] = Str::slug($data['title']); // KALDIRILDI
        $data['is_published'] = $request->has('is_published');
        $data['comments_enabled'] = false; // Yorumlar kapalı

        try {
            $post->update($data);
            
            // Kategoriyi sync et
            if ($categoryId) {
                $post->categories()->sync([$categoryId]);
            } else {
                $post->categories()->sync([]);
            }

            // Destinasyonları sync et
            $post->destinations()->sync($destinationIds);
            
            // Cache temizle
            Cache::forget('posts_' . $post->id);
            Cache::forget('post_' . $post->slug);
            
            Log::info('Blog güncellendi', [
                'post_id' => $post->id,
                'title' => $post->title,
                'has_new_image' => $request->hasFile('cover_image')
            ]);

            return redirect()->route('posts.index')
                ->with('success', 'Blog yazısı başarıyla güncellendi.');
        } catch (\Exception $e) {
            Log::error('Blog güncelleme hatası', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gönderi güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function destroy(Post $post)
    {
        // Görseli sil
        if ($post->cover_image) {
            StorageHelper::deleteFromBoth($post->cover_image);
        }

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Blog yazısı başarıyla silindi.');
    }
}