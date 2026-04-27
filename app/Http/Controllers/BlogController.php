<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Post::published()->latest('published_at');

        if ($request->filled('category')) {
            $categorySlug = $request->string('category')->toString();
            $query->whereHas('taxonomies', function ($q) use ($categorySlug) {
                $q->where('type', 'category')
                    ->where('slug', $categorySlug);
            });
        }

        return view('blog.index', [
            'posts' => $query->paginate(9)->withQueryString(),
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        return view('blog.show', [
            'post' => $post,
            'page' => $post,
        ]);
    }
}
