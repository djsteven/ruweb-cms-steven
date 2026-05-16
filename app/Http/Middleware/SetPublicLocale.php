<?php

namespace App\Http\Middleware;

use App\Models\Locale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPublicLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeLocale = $request->route('locale');
        $locale = is_string($routeLocale) && $routeLocale !== ''
            ? $routeLocale
            : Locale::baseCode();

        app()->setLocale($locale);

        return $next($request);
    }
}
