<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Throwable;

class CacheController extends Controller
{
    public function refresh(): RedirectResponse
    {
        try {
            Artisan::call('optimize:clear');
            Setting::clearCache();
            Artisan::call('optimize');
        } catch (Throwable $exception) {
            Log::error('Failed to refresh application cache from admin bar.', [
                'user_id' => auth()->id(),
                'exception' => $exception,
            ]);

            return back()->with('error', __('admin.cache_refresh_failed'));
        }

        return back()->with('success', __('admin.cache_refresh_success'));
    }
}
