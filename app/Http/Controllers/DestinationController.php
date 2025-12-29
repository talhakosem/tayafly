<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\StorageHelper;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $destinations = Destination::orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('destinations.index', compact('destinations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('destinations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:destinations,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'show_in_menu' => 'nullable|boolean',
        ]);

        // Slug oluştur
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Slug unique kontrolü
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Destination::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Görsel yükleme
        if ($request->hasFile('image')) {
            $data['image'] = StorageHelper::storeAndCopy($request->file('image'), 'destinations');
        }

        // Boolean değerler
        $data['is_active'] = $request->has('is_active');
        $data['show_in_menu'] = $request->has('show_in_menu');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        Destination::create($data);

        return redirect()->route('destinations.index')
            ->with('success', 'Destinasyon başarıyla oluşturuldu.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Destination $destination)
    {
        return view('destinations.edit', compact('destination'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Destination $destination)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:destinations,slug,' . $destination->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'show_in_menu' => 'nullable|boolean',
        ]);

        // Slug oluştur
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Slug unique kontrolü (kendi ID'si hariç)
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Destination::where('slug', $data['slug'])->where('id', '!=', $destination->id)->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Görsel yükleme
        if ($request->hasFile('image')) {
            // Eski görseli sil
            if ($destination->image) {
                StorageHelper::deleteFromBoth($destination->image);
            }
            $data['image'] = StorageHelper::storeAndCopy($request->file('image'), 'destinations');
        }

        // Boolean değerler
        $data['is_active'] = $request->has('is_active');
        $data['show_in_menu'] = $request->has('show_in_menu');

        $destination->update($data);

        return redirect()->route('destinations.index')
            ->with('success', 'Destinasyon güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Destination $destination)
    {
        // Görseli sil
        if ($destination->image) {
            StorageHelper::deleteFromBoth($destination->image);
        }

        $destination->delete();

        return redirect()->route('destinations.index')
            ->with('success', 'Destinasyon silindi.');
    }
}

