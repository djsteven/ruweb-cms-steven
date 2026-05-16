<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use App\Models\Page;
use App\Models\Post;
use App\Models\Taxonomy;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class EditorialControlController extends Controller
{
    public function index(): View
    {
        $locales = Locale::where('is_active', true)->orderBy('sort_order')->get();
        $items = collect()
            ->merge($this->summaries(Page::query()->with('translations')->get(), 'Page', 'title', $locales))
            ->merge($this->summaries(Post::query()->with('translations')->get(), 'Post', 'title', $locales))
            ->merge($this->summaries(Taxonomy::query()->with('translations')->get(), 'Taxonomy', 'name', $locales))
            ->sortBy('label')
            ->values();

        return view('admin.editorial-control.index', compact('items', 'locales'));
    }

    private function summaries(Collection $records, string $type, string $labelField, Collection $locales): Collection
    {
        return $records->groupBy('translation_group_id')->map(function (Collection $group) use ($type, $labelField, $locales) {
            $base = $group->firstWhere('locale', Locale::baseCode()) ?: $group->first();

            return [
                'type' => $type,
                'label' => $base->{$labelField},
                'cells' => $locales->mapWithKeys(function (Locale $locale) use ($base, $type) {
                    $state = $base->derivedTranslationState($locale->code);

                    return [$locale->code => [
                        'state' => $state,
                        'action' => $this->actionFor($type, $base, $locale->code, $state),
                    ]];
                }),
            ];
        })->values();
    }

    /**
     * @return array{kind:string,method:string,url:string}|null
     */
    private function actionFor(string $type, $base, string $localeCode, string $state): ?array
    {
        if ($state === 'missing') {
            return [
                'kind' => 'create',
                'method' => 'post',
                'url' => match ($type) {
                    'Page' => route('admin.pages.translate', [$base, $localeCode]),
                    'Post' => route('admin.posts.translate', [$base, $localeCode]),
                    'Taxonomy' => route('admin.taxonomies.translate', [$base->type, $base, $localeCode]),
                },
            ];
        }

        if ($state === 'outdated') {
            $translation = $base->translations->firstWhere('locale', $localeCode);

            if (! $translation) {
                return null;
            }

            return [
                'kind' => 'update',
                'method' => 'get',
                'url' => match ($type) {
                    'Page' => route('admin.pages.edit', $translation),
                    'Post' => route('admin.posts.edit', $translation),
                    'Taxonomy' => route('admin.taxonomies.edit', [$translation->type, $translation]),
                },
            ];
        }

        return null;
    }
}
