<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Services\Media\MediaHealthService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected MediaHealthService $health
    ) {
    }

    public function index(): View
    {
        return view('admin.dashboard', [
            'mediaCount' => Media::count(),
            'pageCount' => Page::count(),
            'postCount' => Post::count(),
            'publishedPageCount' => Page::where('status', 'published')->count(),
            'publishedPostCount' => Post::where('status', 'published')->count(),
            'mediaHealth' => $this->health->summary(),
        ]);
    }
}
