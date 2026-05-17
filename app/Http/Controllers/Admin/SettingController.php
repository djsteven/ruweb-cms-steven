<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use App\Models\Page;
use App\Models\Setting;
use App\Support\AdminLoginPath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $groupOrder = ['general', 'admin'];
        $groups = Setting::allGrouped()
            ->filter(fn ($v, $k) => ! in_array($k, ['analytics', 'email']))
            ->sortBy(fn ($v, $k) => array_search($k, $groupOrder) !== false ? array_search($k, $groupOrder) : 99);
        $homepageOptions = Page::published()
            ->where('locale', Locale::baseCode())
            ->orderBy('title')
            ->get(['title', 'slug', 'translation_group_id'])
            ->mapWithKeys(fn (Page $page) => [$page->translation_group_id => $page->title . ' (/' . ltrim($page->slug, '/') . ')'])
            ->all();

        $homepageSetting = $groups->get('general')?->firstWhere('key', 'homepage_translation_group_id');
        if ($homepageSetting) {
            if (empty($homepageOptions)) {
                $homepageOptions[$homepageSetting->value] = __('admin.settings_fields.homepage_translation_group_id.empty');
            } elseif (! empty($homepageSetting->value) && ! array_key_exists($homepageSetting->value, $homepageOptions)) {
                $homepageOptions[$homepageSetting->value] = __('admin.settings_fields.homepage_translation_group_id.unpublished');
            }

            $homepageSetting->options = $homepageOptions;
        }

        return view('admin.settings.index', [
            'groups' => $groups,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $allowedHomepageGroups = Page::published()
            ->where('locale', Locale::baseCode())
            ->pluck('translation_group_id')
            ->all();
        $allowedHomepageGroups[] = Setting::get('homepage_translation_group_id');
        $allowedHomepageGroups = array_values(array_unique(array_filter($allowedHomepageGroups)));

        $request->validate([
            'settings.homepage_translation_group_id' => ['sometimes', 'string', Rule::in($allowedHomepageGroups)],
            'settings.admin_login_path' => [
                'sometimes',
                'nullable',
                'string',
                'max:64',
                'regex:/^[A-Za-z0-9-]+$/',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $raw = trim((string) $value);

                    if ($raw === '') {
                        return;
                    }

                    $normalized = AdminLoginPath::normalize($raw);
                    $rawLower = strtolower(trim($raw, '/'));

                    if ($normalized === AdminLoginPath::DEFAULT_SEGMENT && $rawLower !== AdminLoginPath::DEFAULT_SEGMENT) {
                        $fail(__('validation.not_in'));
                    }
                },
            ],
        ]);

        $settings = $request->input('settings', []);

        if (array_key_exists('admin_login_path', $settings)) {
            $settings['admin_login_path'] = AdminLoginPath::normalize($settings['admin_login_path']);
        }

        foreach ($settings as $key => $value) {
            // Don't overwrite password-type settings when field is left blank.
            $existing = Setting::where('key', $key)->first();
            if ($existing && $existing->type === 'password' && ($value === null || $value === '')) {
                continue;
            }
            Setting::set($key, $value);
        }

        Setting::clearCache();

        if (array_key_exists('admin_login_path', $settings)) {
            AdminLoginPath::clearCache();
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', __('admin.settings_saved'));
    }
}
