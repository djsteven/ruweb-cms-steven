<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTaxonomyRequest;
use App\Http\Requests\Admin\UpdateTaxonomyRequest;
use App\Models\Taxonomy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxonomyController extends Controller
{
    public function index(Request $request, string $type): View
    {
        $this->authorize('viewAny', Taxonomy::class);

        $query = Taxonomy::ofType($type)->roots()->ordered()->with('children');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        return view('admin.taxonomies.index', [
            'taxonomies' => $query->paginate(20)->withQueryString(),
            'type'       => $type,
            'totalCount' => Taxonomy::ofType($type)->count(),
        ]);
    }

    public function create(string $type): View
    {
        $this->authorize('create', Taxonomy::class);

        return view('admin.taxonomies.create', [
            'type'    => $type,
            'parents' => Taxonomy::ofType($type)->roots()->ordered()->get(),
        ]);
    }

    public function store(StoreTaxonomyRequest $request, string $type): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Taxonomy::class);

        $taxonomy = Taxonomy::create(array_merge($request->validated(), ['type' => $type]));

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
            'parents'  => Taxonomy::ofType($type)->roots()->ordered()->where('id', '!=', $taxonomy->id)->get(),
        ]);
    }

    public function update(UpdateTaxonomyRequest $request, string $type, Taxonomy $taxonomy): RedirectResponse
    {
        $this->authorize('update', $taxonomy);

        $taxonomy->update($request->validated());

        return redirect()
            ->route('admin.taxonomies.index', $type)
            ->with('success', __('admin.taxonomy_updated'));
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
