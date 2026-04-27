<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Setting;
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
            ->orderBy('title')
            ->get(['title', 'slug'])
            ->mapWithKeys(fn (Page $page) => [$page->slug => $page->title . ' (/' . ltrim($page->slug, '/') . ')'])
            ->all();

        $homepageSetting = $groups->get('general')?->firstWhere('key', 'homepage_slug');
        if ($homepageSetting) {
            if (empty($homepageOptions)) {
                $homepageOptions[$homepageSetting->value ?: 'inicio'] = __('admin.settings_fields.homepage_slug.empty');
            } elseif (! empty($homepageSetting->value) && ! array_key_exists($homepageSetting->value, $homepageOptions)) {
                $homepageOptions[$homepageSetting->value] = $homepageSetting->value . ' (/' . ltrim($homepageSetting->value, '/') . ')';
            }

            $homepageSetting->options = $homepageOptions;
        }

        return view('admin.settings.index', [
            'groups' => $groups,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $allowedHomepageSlugs = Page::published()->pluck('slug')->all();
        $currentHomepageSlug = (string) (Setting::get('homepage_slug', 'inicio') ?: 'inicio');
        $allowedHomepageSlugs[] = $currentHomepageSlug;
        $allowedHomepageSlugs = array_values(array_unique(array_filter($allowedHomepageSlugs)));

        $request->validate([
            'settings.homepage_slug' => ['sometimes', 'string', Rule::in($allowedHomepageSlugs)],
        ]);

        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            // Don't overwrite password-type settings when field is left blank.
            $existing = Setting::where('key', $key)->first();
            if ($existing && $existing->type === 'password' && ($value === null || $value === '')) {
                continue;
            }
            Setting::set($key, $value);
        }

        Setting::clearCache();

        return redirect()
            ->route('admin.settings.index')
            ->with('success', __('admin.settings_saved'));
    }
}
