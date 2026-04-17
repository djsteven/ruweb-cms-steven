<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        return view('admin.analytics.index', [
            'googleTagId' => (string) (Setting::get('google_tag_id') ?? ''),
            'metaPixelId' => (string) (Setting::get('meta_pixel_id') ?? ''),
            'searchConsoleVerificationToken' => (string) (Setting::get('search_console_verification_token') ?? ''),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'google_tag_id' => ['nullable', 'string', 'max:64', 'regex:/^(?:(?:G|GT|AW|DC)-[A-Z0-9-]+)?$/i'],
            'meta_pixel_id' => ['nullable', 'string', 'max:32', 'regex:/^(?:\d+)?$/'],
            'search_console_verification_token' => ['nullable', 'string', 'max:255', 'regex:/^(?!.*<)(?!.*>)(?!.*\s)(?:[A-Za-z0-9_-]+)?$/'],
        ], [
            'google_tag_id.regex' => __('admin.analytics_validation.google_tag_id'),
            'meta_pixel_id.regex' => __('admin.analytics_validation.meta_pixel_id'),
            'search_console_verification_token.regex' => __('admin.analytics_validation.search_console_verification_token'),
        ]);

        $this->persistAnalyticsSetting('google_tag_id', $this->normalizeNullableString($validated['google_tag_id'] ?? null));
        $this->persistAnalyticsSetting('meta_pixel_id', $this->normalizeNullableString($validated['meta_pixel_id'] ?? null));
        $this->persistAnalyticsSetting('search_console_verification_token', $this->normalizeNullableString($validated['search_console_verification_token'] ?? null));
        Setting::clearCache();

        return redirect()
            ->route('admin.analytics.index')
            ->with('success', __('admin.analytics_saved'));
    }

    protected function normalizeNullableString(?string $value): ?string
    {
        $value = $value !== null ? trim($value) : null;

        return $value === '' ? null : $value;
    }

    protected function persistAnalyticsSetting(string $key, ?string $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => 'string',
                'group' => 'analytics',
                'options' => null,
            ]
        );
    }
}
