<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Post;
use App\Models\Taxonomy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Post::class);

        $query = Post::query()->latest();

        if ($request->filled('status') && in_array($request->input('status'), config('cms.statuses'))) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        return view('admin.posts.index', [
            'posts' => $query->paginate(15)->withQueryString(),
            'totalCount' => Post::count(),
            'publishedCount' => Post::where('status', 'published')->count(),
            'draftCount' => Post::where('status', 'draft')->count(),
            'currentStatus' => $request->input('status'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Post::class);

        return view('admin.posts.create', [
            'categories' => Taxonomy::ofType('category')->ordered()->get(),
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $featuredImage = $data['featured_image'] ?? null;
        $categories    = $data['categories'] ?? [];
        unset($data['featured_image'], $data['categories']);

        $post = Post::create($data);

        if ($featuredImage) {
            $post->attachMedia($featuredImage, 'featured_image');
        }

        $post->syncTaxonomies($categories, 'category');

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', __('admin.post_created'));
    }

    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        return view('admin.posts.edit', [
            'post'       => $post,
            'categories' => Taxonomy::ofType('category')->ordered()->get(),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $post);

        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        if ($data['status'] === 'published' && ! $post->published_at) {
            $data['published_at'] = now();
        }

        $featuredImage = $data['featured_image'] ?? null;
        $categories    = $data['categories'] ?? [];
        unset($data['featured_image'], $data['categories']);

        $post->update($data);

        $post->media()->wherePivot('collection', 'featured_image')->detach();
        if ($featuredImage) {
            $post->attachMedia($featuredImage, 'featured_image');
        }

        $post->syncTaxonomies($categories, 'category');

        if ($request->wantsJson()) {
            return response()->json(['saved' => true]);
        }

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', __('admin.post_updated'));
    }

    public function previewRender(Request $request, Post $post): Response
    {
        $post->title = $request->input('title', $post->title);
        $post->excerpt = $request->input('excerpt', $post->excerpt);
        $post->content = $request->input('content', $post->content);

        $html = view('blog.show', ['post' => $post])->render();

        return response($html)->header('Content-Type', 'text/html');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->media()->detach();
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', __('admin.post_deleted'));
    }
}
