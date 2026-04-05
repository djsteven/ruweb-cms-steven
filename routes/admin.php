<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\ClaudeMcpController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware(['guest', 'admin.locale'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
});

// Authenticated admin routes
Route::middleware(['auth', 'role:admin,editor', 'admin.locale'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('claude-mcp', [ClaudeMcpController::class, 'index'])->name('claude-mcp.index');
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/mcp-api-key', [ProfileController::class, 'generateMcpApiKey'])->name('profile.mcp-api-key.generate');
    Route::delete('profile/mcp-api-key', [ProfileController::class, 'revokeMcpApiKey'])->name('profile.mcp-api-key.revoke');

    Route::resource('media', MediaController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy'])
        ->parameter('media', 'media');

    Route::resource('pages', PageController::class)
        ->except(['show']);

    Route::resource('posts', PostController::class)
        ->except(['show']);

    Route::prefix('taxonomies/{type}')->name('taxonomies.')->group(function () {
        Route::get('/', [TaxonomyController::class, 'index'])->name('index');
        Route::get('/create', [TaxonomyController::class, 'create'])->name('create');
        Route::post('/', [TaxonomyController::class, 'store'])->name('store');
        Route::get('/{taxonomy}/edit', [TaxonomyController::class, 'edit'])->name('edit');
        Route::put('/{taxonomy}', [TaxonomyController::class, 'update'])->name('update');
        Route::delete('/{taxonomy}', [TaxonomyController::class, 'destroy'])->name('destroy');
    });

    Route::resource('menus', MenuController::class)->except(['show']);
    Route::put('menus/{menu}/items', [MenuController::class, 'syncItems'])->name('menus.items.sync');

    Route::post('pages/{page}/preview', [PageController::class, 'previewRender'])->name('pages.preview');
    Route::post('posts/{post}/preview', [PostController::class, 'previewRender'])->name('posts.preview');

    // Settings (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)
            ->except(['show']);

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
