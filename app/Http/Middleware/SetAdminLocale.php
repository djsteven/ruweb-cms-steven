<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $locale = Setting::get('admin_locale', 'es');
        } catch (\Throwable) {
            $locale = 'es';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
