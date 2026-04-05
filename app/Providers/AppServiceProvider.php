<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Taxonomy;
use App\Policies\MenuPolicy;
use App\Policies\PostPolicy;
use App\Policies\TaxonomyPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Menu::class, MenuPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Taxonomy::class, TaxonomyPolicy::class);

        View::composer('layouts.public', function ($view) {
            $siteLogo = Setting::get('site_logo');

            $view->with([
                'siteName' => Setting::get('site_name') ?: config('app.name'),
                'siteLogo' => $siteLogo?->url(),
                'footerText' => Setting::get('footer_text'),
                'socialFacebook' => Setting::get('social_facebook'),
                'socialTwitter' => Setting::get('social_twitter'),
                'socialInstagram' => Setting::get('social_instagram'),
            ]);
        });

        View::composer(['admin.layouts.app', 'admin.layouts.guest', 'admin.layouts.editor', 'admin.partials.sidebar', 'admin.partials.navbar', 'admin.auth.*', 'oauth.*'], function ($view) {
            $view->with('siteName', Setting::get('site_name') ?: config('app.name'));
        });
    }
}
