<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $page = Page::where('slug', 'home')->published()->first();

        if (! $page) {
            return view('welcome');
        }

        return view($page->resolveTemplate(), compact('page'));
    }

    public function show(string $slug): View
    {
        $page = Page::where('slug', $slug)->published()->firstOrFail();

        return view($page->resolveTemplate(), compact('page'));
    }
}
