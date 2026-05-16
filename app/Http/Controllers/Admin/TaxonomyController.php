<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTaxonomyRequest;
use App\Http\Requests\Admin\UpdateTaxonomyRequest;
use App\Models\Taxonomy;
use App\Models\Locale;
use App\Services\Content\ContentSchemaRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxonomyController extends Controller
{
    public function index(Request $request, string $type): View
    {
        $this->authorize('viewAny', Taxonomy::class);

        $query = Taxonomy::ofType($type)
            ->where('locale', $request->input('locale', Locale::baseCode()))
            ->roots()
            ->ordered()
            ->with(['children', 'translations']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        return view('admin.taxonomies.index', [
            'taxonomies' => $query->paginate(20)->withQueryString(),
            'type'       => $type,
            'totalCount' => Taxonomy::ofType($type)->count(),
            'locales' => Locale::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function create(string $type): View
    {
        $this->authorize('create', Taxonomy::class);

        return view('admin.taxonomies.create', [
            'type'    => $type,
            'parents' => Taxonomy::ofType($type)->where('locale', Locale::baseCode())->roots()->ordered()->get(),
        ]);
    }

    public function store(StoreTaxonomyRequest $request, string $type): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Taxonomy::class);

        $data = $request->validated();
        $data['locale'] = $data['locale'] ?? Locale::baseCode();
        $taxonomy = Taxonomy::create(array_merge($data, ['type' => $type]));
        $taxonomy->source_fingerprint = $taxonomy->translatableFingerprint();
        $taxonomy->source_field_hashes = $taxonomy->translatableFieldFingerprints();
        $taxonomy->save();

        if ($request->wantsJson()) {
            return response()->json(['id' => $taxonomy->id, 'name' => $taxonomy->name]);
        }

        return redirect()
            ->route('admin.taxonomies.index', $type)
            ->with('success', __('admin.taxonomy_created'));
    }

    public function edit(string $type, Taxonomy $taxonomy): View
    {
        $this->authorize('update', $taxonomy);

        return view('admin.taxonomies.edit', [
            'taxonomy' => $taxonomy,
            'type'     => $type,
            'parents'  => Taxonomy::ofType($type)->where('locale', $taxonomy->locale)->roots()->ordered()->where('id', '!=', $taxonomy->id)->get(),
            'staleFieldNames' => app(ContentSchemaRegistry::class)->formNamesFor($taxonomy, $taxonomy->staleTranslatableFields()),
        ]);
    }

    public function update(UpdateTaxonomyRequest $request, string $type, Taxonomy $taxonomy): RedirectResponse
    {
        $this->authorize('update', $taxonomy);

        $data = $request->validated();
        $data['locale'] = $data['locale'] ?? $taxonomy->locale;
        unset($data['acknowledged_fields']);
        $taxonomy->update($data);
        if ($taxonomy->isBaseLocale()) {
            $taxonomy->source_fingerprint = $taxonomy->translatableFingerprint();
            $taxonomy->source_field_hashes = $taxonomy->translatableFieldFingerprints();
        } else {
            $taxonomy->syncTranslationFromBase();
        }
        $taxonomy->save();

        return redirect()
            ->route('admin.taxonomies.index', $type)
            ->with('success', __('admin.taxonomy_updated'));
    }

    public function translate(Request $request, string $type, Taxonomy $taxonomy, string $locale): RedirectResponse
    {
        $this->authorize('create', Taxonomy::class);
        abort_unless(Locale::where('code', $locale)->where('is_active', true)->exists(), 404);

        $existing = $taxonomy->translations()->where('locale', $locale)->first();
        if ($existing) {
            return redirect()->route('admin.taxonomies.edit', [$type, $existing]);
        }

        $base = $taxonomy->baseTranslation() ?: $taxonomy;
        $translation = $base->replicate(['slug']);
        $translation->locale = $locale;
        $translation->slug = $this->uniqueTranslatedSlug($base->slug, $base->type, $locale);
        $translation->parent_id = $this->translatedParentId($base, $locale);
        $translation->translation_status = 'needs_review';
        $translation->source_fingerprint = $base->translatableFingerprint();
        $translation->source_field_hashes = $base->translatableFieldFingerprints();
        $translation->save();

        return redirect()
            ->route('admin.taxonomies.edit', [$type, $translation])
            ->with('success', __('admin.taxonomy_created'));
    }

    private function uniqueTranslatedSlug(string $slug, string $type, string $locale): string
    {
        $candidate = $slug;
        $counter = 2;

        while (Taxonomy::where('type', $type)->where('locale', $locale)->where('slug', $candidate)->exists()) {
            $candidate = $slug.'-'.$counter;
            $counter++;
        }

        return $candidate;
    }

    private function translatedParentId(Taxonomy $base, string $locale): ?int
    {
        if (! $base->parent_id) {
            return null;
        }

        $parent = $base->parent()->with('translations')->first();

        return $parent?->translations->firstWhere('locale', $locale)?->id;
    }

    public function destroy(string $type, Taxonomy $taxonomy): RedirectResponse
    {
        $this->authorize('delete', $taxonomy);

        $taxonomy->children()->update(['parent_id' => $taxonomy->parent_id]);
        $taxonomy->delete();

        return redirect()
            ->route('admin.taxonomies.index', $type)
            ->with('success', __('admin.taxonomy_deleted'));
    }
}
