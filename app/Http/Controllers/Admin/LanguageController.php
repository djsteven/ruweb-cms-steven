<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LanguageController extends Controller
{
    public function index(): View
    {
        $locales = Locale::orderBy('sort_order')->get();

        $available = collect(Locale::catalog())
            ->reject(fn (string $name, string $code): bool => $locales->contains('code', $code))
            ->all();

        return view('admin.languages.index', compact('locales', 'available'));
    }

    public function store(Request $request): RedirectResponse
    {
        $catalog = Locale::catalog();

        $data = $request->validate([
            'code' => [
                'required',
                'string',
                Rule::in(array_keys($catalog)),
                Rule::unique('locales', 'code'),
            ],
        ]);

        Locale::create([
            'code' => $data['code'],
            'name' => $catalog[$data['code']],
            'is_base' => false,
            'is_active' => true,
            'is_public' => false,
            'sort_order' => (int) Locale::max('sort_order') + 1,
        ]);

        return redirect()->route('admin.languages.index')->with('success', __('admin.language_added'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'locales' => ['array'],
            'locales.*.is_active' => ['nullable', 'boolean'],
            'locales.*.is_public' => ['nullable', 'boolean'],
        ]);

        foreach (Locale::all() as $locale) {
            if ($locale->is_base) {
                $locale->update(['is_active' => true, 'is_public' => true]);
                continue;
            }

            $input = $data['locales'][$locale->code] ?? [];
            $isActive = (bool) ($input['is_active'] ?? false);

            $locale->update([
                'is_active' => $isActive,
                'is_public' => $isActive && (bool) ($input['is_public'] ?? false),
            ]);
        }

        return redirect()->route('admin.languages.index')->with('success', __('admin.settings_saved'));
    }
}
