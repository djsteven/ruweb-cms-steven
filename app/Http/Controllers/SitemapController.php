<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('sitemap-xml', 3600, function () {
            $homepageSlug = (string) (Setting::get('homepage_slug', 'inicio') ?: 'inicio');
            $homePage = Page::published()->where('slug', $homepageSlug)->first();
            $pages    = Page::published()->where('slug', '!=', $homepageSlug)->get();
            $posts    = Post::published()->latest('published_at')->get();
            $latestPostDate = $posts->first()?->updated_at;

            return view('sitemap.index', compact('homePage', 'pages', 'posts', 'latestPostDate'))->render();
        });

        return response($xml, 200, ['Content-Type' => 'text/xml; charset=utf-8']);
    }
}
