<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/oauth.php'));

            Route::prefix('mcp')
                ->middleware('mcp.auth')
                ->group(base_path('routes/mcp.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'         => \App\Http\Middleware\RoleMiddleware::class,
            'admin.locale' => \App\Http\Middleware\SetAdminLocale::class,
            'public.locale' => \App\Http\Middleware\SetPublicLocale::class,
            'mcp.auth'     => \App\Http\Middleware\AuthenticateMcpApiKey::class,
        ]);
        $middleware->redirectGuestsTo(fn () => '/');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return response()->view('errors.404', [], 404);
            }

            return null;
        });
    })->create();
