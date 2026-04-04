# Collections Guide

This document defines the rules and patterns for creating new content collections in Flaxt CMS. The blog (`posts`) is the reference implementation — every future collection must follow these conventions.

---

## Mandatory rules

These rules are non-negotiable. Any collection that violates them is considered broken.

### 1. Never hardcode images

**Every image in the system must live in the media library and be referenced via the polymorphic `mediables` relationship.** No exceptions.

Forbidden:

```php
// NEVER — hardcoded path
<img src="/images/hero.jpg">

// NEVER — path stored as string in a column or JSON
'banner' => '/storage/uploads/banner.jpg'

// NEVER — external URL stored as content
'image' => 'https://example.com/photo.jpg'
```

Correct:

```php
// Model references media via collection key
$item->featuredImage()           // uses HasMedia trait
$item->mediaByCollection('gallery')  // custom collection

// Blade template
<img src="{{ $item->featuredImage()->url() }}" alt="{{ $item->featuredImage()->alt }}">
```

**Why:** The media library provides deduplication, alt/title metadata for accessibility, disk abstraction, orphan auditing, and a single source of truth. Hardcoded paths bypass all of this and break when the storage strategy changes.

### 2. Always use the media library (HasMedia trait)

Every model that displays images or documents must:

1. Use the `HasMedia` trait.
2. Reference media via `attachMedia($id, $collection)` — never store file paths.
3. Validate media fields as `['nullable', 'integer', 'exists:media,id']` in FormRequests.
4. Use the `@include('admin.media._selector')` component in admin forms.

```php
use App\Traits\HasMedia;

class Product extends Model
{
    use HasMedia;
}
```

**Collection keys** are free-form strings that group media per purpose. Standard keys:

| Key | Purpose |
|-----|---------|
| `featured_image` | Primary image for listings and SEO |
| `gallery` | Multiple images for a detail view |
| `document` | Attached PDF or file |

You can define any key your collection needs. Keep them `snake_case` and descriptive.

**Attaching media in controllers:**

```php
// Store
$featuredImage = $data['featured_image'] ?? null;
unset($data['featured_image']);

$item = MyModel::create($data);

if ($featuredImage) {
    $item->attachMedia($featuredImage, 'featured_image');
}

// Update — always detach first, then re-attach
$item->media()->wherePivot('collection', 'featured_image')->detach();
if ($featuredImage) {
    $item->attachMedia($featuredImage, 'featured_image');
}
```

**Rendering in Blade:**

```blade
@if($item->featuredImage())
    <img
        src="{{ $item->featuredImage()->url() }}"
        alt="{{ $item->featuredImage()->alt ?: $item->title }}"
    >
@endif
```

Always provide a fallback for `alt` using the item's title. Always check `@if` before accessing the media — featured images are nullable.

### 3. Always use the taxonomy system (HasTaxonomies trait)

If a collection needs classification (categories, tags, topics, regions, etc.), use the polymorphic taxonomy system. Never create separate category tables per collection.

```php
use App\Traits\HasTaxonomies;

class Product extends Model
{
    use HasTaxonomies;
}
```

**Rules:**

- Use `syncTaxonomies($ids, $type)` in store/update — it only touches terms of the given type.
- Pass taxonomy IDs from the form, validate as `['nullable', 'array']` + `['categories.*' => 'integer|exists:taxonomies,id']`.
- Add the checkbox list with inline-create input in the admin form partial (copy from `posts/_form.blade.php`).
- Register new taxonomy types in the sidebar under their collection's collapsible group (see `taxonomies.md` for details).
- No new migrations needed — the `taxonomies` table handles all types via the `type` column.

**Controller pattern:**

```php
$categories = $data['categories'] ?? [];
unset($data['categories']);

$item = MyModel::create($data);
$item->syncTaxonomies($categories, 'category');
```

### 4. Follow the status + published_at pattern

Every collection must use:

```php
$table->string('status')->default('draft');        // 'draft' or 'published'
$table->timestamp('published_at')->nullable();     // when it became public
```

And a `published()` scope:

```php
public function scopePublished($query)
{
    return $query->where('status', 'published')
        ->where('published_at', '<=', now());
}
```

Public controllers must always use `->published()` — never show drafts to visitors.

### 5. The edit view must use the live editor layout

Every collection's `edit.blade.php` **must** extend `admin.layouts.editor` and implement live preview. This is a core system feature — not optional. The create view uses the standard `admin.layouts.app` layout (no preview needed for unsaved items).

**Why:** The side-panel + live iframe preview is the central editorial UX of this CMS. A collection without it is an incomplete implementation.

**The edit view must:**

1. `@extends('admin.layouts.editor')` — not `admin.layouts.app`.
2. Register a `previewRender` method in the admin controller.
3. Register a preview route (`POST /{model}/preview`) in `routes/admin.php`.
4. Include the JS block that wires dirty detection, localStorage draft, preview refresh, and save via `fetch`.

**Controller — previewRender method:**

The method receives the current form data, mutates the model in memory (without saving), renders the public view, and returns raw HTML:

```php
public function previewRender(Request $request, Product $product): Response
{
    // Override only the fields that can change in the editor
    $product->title   = $request->input('title', $product->title);
    $product->excerpt = $request->input('excerpt', $product->excerpt);
    $product->content = $request->input('content', $product->content);
    // add any other editable fields

    $html = view('products.show', ['product' => $product])->render();

    return response($html)->header('Content-Type', 'text/html');
}
```

**Route:**

```php
// routes/admin.php — inside the admin middleware group
Route::resource('products', ProductController::class)->except(['show']);
Route::post('products/{product}/preview', [ProductController::class, 'previewRender'])->name('products.preview');
```

**edit.blade.php structure:**

```blade
@php
    $editorBackHref  = route('admin.products.index');
    $editorBackTitle = __('admin.back_to_products');
    $showPreview     = true;
@endphp

@extends('admin.layouts.editor')

@section('editor-title', $product->title)

@section('editor-actions')
    @if ($product->isPublished())
        <a href="{{ route('products.show', $product->slug) }}" target="_blank"
           class="text-xs text-gray-500 hover:text-gray-300 transition-colors hidden sm:inline">
            {{ __('admin.view_live') }}
        </a>
    @endif
@endsection

@section('editor-form')
    @include('admin.products._form', ['product' => $product])
@endsection

@section('editor-footer')
    <div class="flex-none px-5 py-3 border-t border-white/[0.06] bg-[#111111]">
        @if ($product->isPublished())
            <button type="button" id="update-btn" disabled
                    class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-medium rounded-md transition-colors">
                {{ __('admin.btn_save_changes') }}
            </button>
        @else
            <div class="flex gap-2">
                <button type="button" id="save-draft-btn" disabled
                        class="flex-1 px-3 py-2 bg-white/5 hover:bg-white/10 disabled:opacity-40 disabled:cursor-not-allowed text-gray-300 text-sm font-medium rounded-md transition-colors">
                    {{ __('admin.btn_save_draft') }}
                </button>
                <button type="button" id="publish-btn"
                        class="flex-1 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors">
                    {{ __('admin.btn_publish') }}
                </button>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const form          = document.getElementById('editor-form');
    const iframe        = document.getElementById('preview-frame');
    const previewUrl    = '{{ route('admin.products.preview', $product) }}';
    const csrfToken     = document.querySelector('meta[name="csrf-token"]').content;
    const headerSaveBtn = document.getElementById('save-btn');
    const updateBtn     = document.getElementById('update-btn');
    const saveDraftBtn  = document.getElementById('save-draft-btn');
    const publishBtn    = document.getElementById('publish-btn');
    const DRAFT_KEY     = 'product-draft-{{ $product->id }}';

    let previewTimer;
    let savedSnapshot = formSnapshot();

    function formSnapshot() {
        const data = new FormData(form);
        data.delete('_token');
        data.delete('_method');
        const entries = [];
        for (const [k, v] of data.entries()) entries.push(k + '=' + v);
        return entries.join('&');
    }

    function isDirty()         { return formSnapshot() !== savedSnapshot; }
    function updateDirtyState() {
        const dirty = isDirty();
        if (updateBtn)    updateBtn.disabled    = !dirty;
        if (saveDraftBtn) saveDraftBtn.disabled = !dirty;
        headerSaveBtn.disabled = !dirty;
    }

    function saveDraft() {
        const data = {};
        new FormData(form).forEach((v, k) => {
            if (k === '_token' || k === '_method') return;
            data[k] = v;
        });
        localStorage.setItem(DRAFT_KEY, JSON.stringify(data));
    }

    function restoreDraft() {
        const raw = localStorage.getItem(DRAFT_KEY);
        if (!raw) return;
        try {
            const data = JSON.parse(raw);
            for (const [k, v] of Object.entries(data)) {
                const el = form.querySelector('[name="' + CSS.escape(k) + '"]');
                if (!el) continue;
                if (el.tagName === 'SELECT' || el.tagName === 'TEXTAREA') el.value = v;
                else if (el.type !== 'hidden') el.value = v;
            }
        } catch (_) {}
    }

    function clearDraft() { localStorage.removeItem(DRAFT_KEY); }

    function refreshPreview() {
        const previewData = new FormData(form);
        previewData.delete('_method');
        fetch(previewUrl, {
            method: 'POST',
            body: previewData,
            headers: { 'X-CSRF-TOKEN': csrfToken },
        }).then(res => {
            if (res.ok) return res.text().then(html => { iframe.srcdoc = html; });
        }).catch(() => {});
    }

    async function save(statusOverride) {
        if (updateBtn)    updateBtn.disabled    = true;
        if (saveDraftBtn) saveDraftBtn.disabled = true;
        headerSaveBtn.disabled = true;

        if (statusOverride) {
            const statusSelect = form.querySelector('[name="status"]');
            if (statusSelect) statusSelect.value = statusOverride;
        }

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            });
            if (res.ok) {
                savedSnapshot = formSnapshot();
                clearDraft();
                showToast('{{ __('admin.saved_success') }}');
                updateDirtyState();
            } else {
                showToast('{{ __('admin.save_error') }}', 'error');
                updateDirtyState();
            }
        } catch (_) {
            showToast('{{ __('admin.save_error') }}', 'error');
            updateDirtyState();
        }
    }

    function onFormChange() {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(refreshPreview, 600);
        saveDraft();
        updateDirtyState();
    }

    form.addEventListener('input',  onFormChange);
    form.addEventListener('change', onFormChange);

    if (updateBtn)   updateBtn.addEventListener('click',   () => save());
    if (saveDraftBtn) saveDraftBtn.addEventListener('click', () => save('draft'));
    if (publishBtn)   publishBtn.addEventListener('click',   () => save('published'));
    headerSaveBtn.addEventListener('click', () => save());

    restoreDraft();
    updateDirtyState();
    refreshPreview();
})();
</script>
@endpush
```

The `update` method in the admin controller must return JSON when the request expects it (the editor saves via `fetch` with `Accept: application/json`):

```php
public function update(UpdateProductRequest $request, Product $product): RedirectResponse|JsonResponse
{
    // ... update logic ...

    if ($request->wantsJson()) {
        return response()->json(['saved' => true]);
    }

    return redirect()->route('admin.products.edit', $product)->with('success', __('admin.product_updated'));
}
```

### 6. Use meta_json for SEO metadata

Every collection that has public-facing pages must include a `meta_json` JSON column with this shape:

```json
{
    "description": "Page description for SEO",
    "og_title": "Override for social sharing title (optional)",
    "og_description": "Override for social sharing description (optional)"
}
```

And a `meta()` accessor:

```php
public function meta(): array
{
    return $this->meta_json ?? [];
}
```

This keeps SEO consistent with pages and the `seo-meta` Blade component.

---

## Collection creation checklist

Follow this order when creating a new collection (e.g., `products`):

### Step 1 — Migration + Model

```bash
php artisan make:model Product -m
```

**Migration must include:**

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    // ... collection-specific fields ...
    $table->json('meta_json')->nullable();
    $table->string('status')->default('draft');
    $table->timestamp('published_at')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
});
```

**Model must include:**

```php
use App\Traits\HasMedia;
use App\Traits\HasTaxonomies;

class Product extends Model
{
    use HasMedia, HasTaxonomies;

    protected $fillable = ['title', 'slug', /* ... */ 'meta_json', 'status', 'published_at', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return [
            'meta_json' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished($query) { /* ... */ }
    public function meta(): array { return $this->meta_json ?? []; }
    public function url(): string { return route('products.show', $this->slug); }
    public function isPublished(): bool { /* ... */ }
    public function creator(): BelongsTo { /* ... */ }
    public function editor(): BelongsTo { /* ... */ }
}
```

### Step 2 — FormRequests

Create `StoreProductRequest` and `UpdateProductRequest`:

```php
public function rules(): array
{
    return [
        'title'          => ['required', 'string', 'max:255'],
        'slug'           => ['required', 'string', 'max:255', 'unique:products,slug'],
        'status'         => ['required', 'in:draft,published'],
        'published_at'   => ['nullable', 'date'],
        'featured_image' => ['nullable', 'integer', 'exists:media,id'],
        'categories'     => ['nullable', 'array'],
        'categories.*'   => ['integer', 'exists:taxonomies,id'],
        'meta_json'      => ['nullable', 'array'],
        // ... collection-specific rules ...
    ];
}
```

### Step 3 — Admin controller

Follow the `PostController` pattern exactly:

- `index` — list with status filter and search
- `create` — pass taxonomies to view
- `store` — create model, attach media, sync taxonomies
- `edit` — pass model and taxonomies to view
- `update` — update model, detach/re-attach media, sync taxonomies; **must return JSON when `$request->wantsJson()`**
- `previewRender` — mutate model in memory, render public view, return raw HTML response
- `destroy` — detach media, delete model (admin only)

### Step 4 — Policy

```php
class ProductPolicy
{
    public function viewAny(User $user): bool    { return true; }
    public function create(User $user): bool     { return true; }
    public function update(User $user): bool     { return true; }
    public function delete(User $user): bool     { return $user->role === 'admin'; }
}
```

Register in `AuthServiceProvider`. Editors can create/update but only admins can delete.

### Step 5 — Admin views

```
resources/views/admin/products/
├── index.blade.php       ← extends admin.layouts.app
├── create.blade.php      ← extends admin.layouts.app
├── edit.blade.php        ← extends admin.layouts.editor (live preview)
└── _form.blade.php
```

The `_form.blade.php` must include:
- `@include('admin.media._selector', [...])` for featured image
- Taxonomy checkbox list with inline-create input
- SEO meta fields

`edit.blade.php` must extend `admin.layouts.editor` with the full JS block (dirty detection, localStorage draft, preview refresh, async save). See rule 5 above for the complete template.

### Step 6 — Routes

**Admin routes** (in `routes/admin.php`, inside the admin middleware group):

```php
Route::resource('products', ProductController::class)->except(['show']);
Route::post('products/{product}/preview', [ProductController::class, 'previewRender'])->name('products.preview');
```

**Public routes** (MUST be registered before the page catch-all route):

```php
Route::get('/products', [ProductFrontController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductFrontController::class, 'show'])->name('products.show');
```

### Step 7 — Public controller + views

```php
class ProductFrontController extends Controller
{
    public function index()
    {
        $products = Product::published()->latest('published_at')->paginate(12);
        return view('products.index', compact('products'));
    }

    public function show(string $slug)
    {
        $product = Product::published()->where('slug', $slug)->firstOrFail();
        return view('products.show', compact('product'));
    }
}
```

### Step 8 — Sidebar navigation

Add the collection group to `resources/views/admin/partials/sidebar.blade.php` with taxonomy sub-items nested inside (see `taxonomies.md` for sidebar placement rules).

### Step 9 — MCP endpoints (optional)

If the collection should be accessible via MCP, add endpoints in `routes/mcp.php` and handle them in `McpController` following the existing pages/posts pattern.

### Step 10 — Tests + documentation

- Write feature tests for auth boundaries (editor vs admin).
- Add a doc file `docs/{collection-name}.md` following the `blog-collection.md` format.

---

## Quick reference: what goes where

| Concern | Mechanism | Never do |
|---------|-----------|----------|
| Images / files | `HasMedia` trait + `mediables` pivot | Store file paths in columns or JSON |
| Classification | `HasTaxonomies` trait + `taxables` pivot | Create separate category tables |
| SEO metadata | `meta_json` column with standard keys | Custom SEO columns per model |
| Visibility | `status` + `published_at` + `published()` scope | Boolean `is_active` or no gating |
| Authorship | `created_by` / `updated_by` FK to users | No audit trail |
| Permissions | Policy class (editor CRUD, admin delete) | Inline role checks in controllers |
| Admin forms | Shared partials (`_selector`, `_form`) | Duplicate media/taxonomy UI code |
| Edit view | `admin.layouts.editor` + live preview JS | `admin.layouts.app` on the edit view |
| Save from editor | `fetch` with `Accept: application/json` → JSON response | Full page reload on save |
| Preview | `previewRender` method + POST preview route | No preview or static screenshots |
