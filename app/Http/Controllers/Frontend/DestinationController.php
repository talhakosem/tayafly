<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Post;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * Display the specified destination.
     */
    public function show(string $slug)
    {
        $destination = Destination::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $posts = $destination->posts()
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('frontend.destination.show', [
            'destination' => $destination,
            'posts' => $posts,
        ]);
    }

    /**
     * Display a blog post under a destination.
     */
    public function showPost(string $destination_slug, string $blog_slug)
    {
        $destination = Destination::where('slug', $destination_slug)
            ->where('is_active', true)
            ->firstOrFail();

        $post = Post::where('slug', $blog_slug)
            ->where('is_published', true)
            ->whereHas('destinations', function($q) use ($destination) {
                $q->where('destinations.id', $destination->id);
            })
            ->with(['categories', 'destinations'])
            ->firstOrFail();

        return view('frontend.blog.show', [
            'post' => $post,
            'destination' => $destination,
        ]);
    }
}

