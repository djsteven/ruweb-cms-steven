<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Models\Locale;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

$catalogLocales = Locale::catalogCodes();
$localePattern = implode('|', array_map(
    fn (string $code): string => preg_quote($code, '/'),
    $catalogLocales
));
$reservedSlugs = 'admin|mcp|authorize|token|\.well-known';
$reservedLocaleSlugs = implode('|', array_map(
    fn (string $code): string => preg_quote($code, '/').'$',
    $catalogLocales
));

Route::middleware('public.locale')->group(function () use ($localePattern, $reservedSlugs, $reservedLocaleSlugs) {
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

    Route::prefix('{locale}')
        ->where(['locale' => $localePattern])
        ->group(function () use ($reservedSlugs) {
            Route::get('/', [PageController::class, 'home'])->name('localized.home');
            Route::get('/blog', [BlogController::class, 'index'])->name('localized.blog.index');
            Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('localized.blog.show');
            Route::get('/{slug}', [PageController::class, 'show'])
                ->name('localized.page.show')
                ->where('slug', '^(?!('.$reservedSlugs.'))[a-zA-Z0-9\-\/]+$');
        });

    Route::get('/{slug}', [PageController::class, 'show'])
        ->name('page.show')
        ->where('slug', '^(?!('.$reservedSlugs.'|'.$reservedLocaleSlugs.'))[a-zA-Z0-9\-\/]+$');
});
