<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $homepageSlug = (string) (Setting::get('homepage_slug', 'inicio') ?: 'inicio');
        $page = Page::where('slug', $homepageSlug)->published()->first();

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
