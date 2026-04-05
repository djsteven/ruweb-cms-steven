<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show')->where('slug', '^(?!(admin|mcp|authorize|token|\.well-known))[a-zA-Z0-9\-\/]+$');
