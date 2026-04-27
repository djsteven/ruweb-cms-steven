<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        $query = Page::query()->latest();

        if ($request->filled('status') && in_array($request->input('status'), config('cms.statuses'))) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        return view('admin.pages.index', [
            'pages' => $query->paginate(15)->withQueryString(),
            'totalCount' => Page::count(),
            'publishedCount' => Page::where('status', 'published')->count(),
            'draftCount' => Page::where('status', 'draft')->count(),
            'currentStatus' => $request->input('status'),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.create', [
            'templates' => config('cms.templates'),
        ]);
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $featuredImage = $data['featured_image'] ?? null;
        unset($data['featured_image']);

        $page = Page::create($data);

        if ($featuredImage) {
            $page->attachMedia($featuredImage, 'featured_image');
        }

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', __('admin.page_created'));
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', [
            'page' => $page,
            'templates' => config('cms.templates'),
        ]);
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        if ($data['status'] === 'published' && ! $page->published_at) {
            $data['published_at'] = now();
        }

        $featuredImage = $data['featured_image'] ?? null;
        unset($data['featured_image']);

        $page->update($data);

        $page->media()->wherePivot('collection', 'featured_image')->detach();
        if ($featuredImage) {
            $page->attachMedia($featuredImage, 'featured_image');
        }

        if ($request->wantsJson()) {
            return response()->json(['saved' => true]);
        }

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', __('admin.page_updated'));
    }

    public function previewRender(Request $request, Page $page): Response
    {
        $page->title = $request->input('title', $page->title);
        $page->template_key = $request->input('template_key', $page->template_key);
        $page->content_json = $request->input('content_json', $page->content_json ?? []);

        $html = view($page->previewView(), $page->previewData())->render();

        return response($html)->header('Content-Type', 'text/html');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->media()->detach();
        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', __('admin.page_deleted'));
    }
}
