# Taxonomy System

Flaxt CMS includes a generic, reusable taxonomy system for classifying content. It is modelled after the `media`/`mediables` polymorphic pattern already present in the project.

---

## Database schema

### `taxonomies`

Stores the taxonomy terms (e.g. a specific category, tag, or any other type).

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | string | Display name |
| `slug` | string | URL-safe identifier, unique per `type` |
| `type` | string | Discriminator: `category`, `tag`, … |
| `parent_id` | bigint FK nullable | Self-referential hierarchy |
| `description` | text nullable | |
| `order` | int unsigned | Sort weight within a type |
| `created_at` / `updated_at` | timestamps | |

**Unique constraint:** `(slug, type)` — the same slug can exist in different types.

### `taxables`

Pivot table connecting any Eloquent model to taxonomy terms.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `taxonomy_id` | bigint FK → taxonomies | Cascade on delete |
| `taxable_id` | bigint | Polymorphic |
| `taxable_type` | string | Polymorphic |
| `created_at` / `updated_at` | timestamps | |

**Unique constraint:** `(taxonomy_id, taxable_id, taxable_type)`.

---

## Core components

### `App\Models\Taxonomy`

```php
Taxonomy::ofType('category')->ordered()->get();   // all categories, sorted
Taxonomy::ofType('tag')->roots()->get();           // top-level tags only
$taxonomy->children;                               // HasMany children
$taxonomy->parent;                                 // BelongsTo parent
```

**Scopes:** `ofType(string)`, `roots()`, `ordered()`.

### `App\Traits\HasTaxonomies`

Add this trait to any Eloquent model that needs taxonomy support.

```php
use App\Traits\HasTaxonomies;

class Post extends Model
{
    use HasTaxonomies;
}
```

**Available methods:**

```php
$post->taxonomies();                    // MorphToMany — all types
$post->taxonomiesByType('category');    // MorphToMany — filtered by type
$post->categories();                    // shorthand for taxonomiesByType('category')
$post->syncTaxonomies([1, 3], 'category'); // replace this model's categories
```

`syncTaxonomies` only touches terms of the given type, leaving other types untouched.

---

## Adding a new taxonomy type

1. **Choose a `type` string** — use lowercase snake_case (e.g. `region`, `industry`).

2. **Add translations** in `lang/en/admin.php` and `lang/es/admin.php`:
   ```php
   'taxonomy_type_region' => 'Regions',
   ```

3. **Expose it in the sidebar.** Taxonomy links live as subitems inside their parent collection's collapsible group, not as standalone top-level items. Find the corresponding `<ul id="…-group">` block in `resources/views/admin/partials/sidebar.blade.php` and add:
   ```html
   <li>
       <a href="{{ route('admin.taxonomies.index', 'region') }}" …>Regions</a>
   </li>
   ```
   The group auto-expands when any of its routes is active; the toggle is handled by the `data-sidebar-toggle` JS in the same file.

4. **Use it in a model.** Apply `HasTaxonomies` to the model and call `syncTaxonomies($ids, 'region')` in the controller on store/update.

5. **Add to forms.** Include a checkbox list with an inline-create input in the relevant admin form partial. See the pattern in `resources/views/admin/posts/_form.blade.php` — the inline create block POSTs JSON to `route('admin.taxonomies.store', $type)` and appends a checked checkbox on success. Always include it regardless of whether existing terms exist (show a hint when the list is empty).

No migrations are needed — the `taxonomies` table handles every type via the `type` column.

---

## Adding a new model to an existing taxonomy type

Say you want to attach `Page` models to categories.

1. Add `use HasTaxonomies;` to `App\Models\Page`.
2. In `PageController@create` and `edit`, pass `$categories` to the view.
3. In `PageController@store` and `update`, call `$page->syncTaxonomies($data['categories'] ?? [], 'category')`.
4. Add the category selector to `resources/views/admin/pages/_form.blade.php` (copy the block from the posts form).
5. Add `'categories'` validation to `StorePageRequest` and `UpdatePageRequest`.

---

## Conventions

- **Type names** are singular lowercase English words: `category`, `tag`, `topic`, `region`.
- **Slugs** must be unique within a type and match `^[a-z0-9]+(?:-[a-z0-9]+)*$`.
- **Hierarchy** is supported but optional. Use `parent_id` for nested structures (e.g. subcategories). Maximum nesting depth is not enforced but keep it shallow (≤ 2 levels) for UI clarity.
- **Deletion** of a parent promotes its children to the grandparent level (or root). This is handled by the controller automatically.
- **Authorization** follows the same role model as the rest of the CMS: editors and admins can create/update; only admins can delete.
- **Sidebar placement:** taxonomy links are always subitems inside their collection's collapsible group, never standalone top-level items. This keeps the navigation flat as collections grow.
- **Inline create:** every form that shows a taxonomy checkbox list must include an inline-create input below the list. The input POSTs JSON to the `store` endpoint and appends a checked checkbox on success without a page reload. Never require the user to leave the content form to create a taxonomy term.

---

## Querying from public controllers

```php
// Posts in a given category
Post::published()
    ->whereHas('taxonomies', fn ($q) => $q->where('slug', $slug)->where('type', 'category'))
    ->latest('published_at')
    ->paginate(10);

// All categories that have at least one published post
Taxonomy::ofType('category')
    ->whereHas('taxables', fn ($q) => $q->where('taxable_type', Post::class))
    ->ordered()
    ->get();
```
