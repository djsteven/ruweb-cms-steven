<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuRequest;
use App\Http\Requests\Admin\SyncMenuItemsRequest;
use App\Http\Requests\Admin\UpdateMenuRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Taxonomy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Menu::class);

        $menus = Menu::withCount('items')->latest()->paginate(20);

        return view('admin.menus.index', compact('menus'));
    }

    public function create(): View
    {
        $this->authorize('create', Menu::class);

        return view('admin.menus.create');
    }

    public function store(StoreMenuRequest $request): RedirectResponse
    {
        $this->authorize('create', Menu::class);

        $menu = Menu::create($request->validated());

        return redirect()->route('admin.menus.edit', $menu)
            ->with('success', __('admin.menu_created'));
    }

    public function edit(Menu $menu): View
    {
        $this->authorize('update', $menu);

        return view('admin.menus.edit', [
            'menu'       => $menu,
            'menuItems'  => $menu->tree(),
            'pages'      => Page::published()->orderBy('title')->get(['id', 'title', 'slug']),
            'posts'      => Post::published()->orderBy('title')->get(['id', 'title', 'slug']),
            'taxonomies' => Taxonomy::ordered()->get(['id', 'name', 'slug', 'type']),
        ]);
    }

    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        $this->authorize('update', $menu);

        $menu->update($request->validated());

        return redirect()->route('admin.menus.edit', $menu)
            ->with('success', __('admin.menu_updated'));
    }

    public function syncItems(SyncMenuItemsRequest $request, Menu $menu): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $menu);

        DB::transaction(function () use ($request, $menu) {
            $menu->items()->delete();

            $items = $request->validated()['items'];
            $idMap = []; // maps temp client IDs to real DB IDs

            foreach ($items as $itemData) {
                $menuItem = MenuItem::create([
                    'menu_id'       => $menu->id,
                    'parent_id'     => null, // resolved in second pass
                    'label'         => $itemData['label'],
                    'type'          => $itemData['type'],
                    'linkable_type' => $this->resolveLinkableType($itemData['type']),
                    'linkable_id'   => $itemData['type'] !== 'custom_link' ? ($itemData['linkable_id'] ?? null) : null,
                    'url'           => $itemData['type'] === 'custom_link' ? ($itemData['url'] ?? null) : null,
                    'target'        => $itemData['target'],
                    'order'         => $itemData['order'],
                ]);

                if (isset($itemData['id'])) {
                    $idMap[$itemData['id']] = $menuItem->id;
                }
            }

            // Second pass: resolve parent_id references
            foreach ($items as $itemData) {
                if (!empty($itemData['parent_id']) && isset($idMap[$itemData['parent_id']]) && isset($itemData['id']) && isset($idMap[$itemData['id']])) {
                    MenuItem::where('id', $idMap[$itemData['id']])
                        ->update(['parent_id' => $idMap[$itemData['parent_id']]]);
                }
            }
        });

        if ($request->wantsJson()) {
            return response()->json(['saved' => true]);
        }

        return redirect()->route('admin.menus.edit', $menu)
            ->with('success', __('admin.menu_items_saved'));
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $this->authorize('delete', $menu);

        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', __('admin.menu_deleted'));
    }

    private function resolveLinkableType(string $type): ?string
    {
        return match ($type) {
            'page'     => Page::class,
            'post'     => Post::class,
            'taxonomy' => Taxonomy::class,
            default    => null,
        };
    }
}
