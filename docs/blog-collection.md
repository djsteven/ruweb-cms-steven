# Blog Collection Reference

This document describes the `blog` collection added in phase 4 and how to replicate the same pattern for future collections.

## Domain Model

`posts` table:

- `title` (string)
- `slug` (unique string)
- `excerpt` (text, optional)
- `content` (longText, optional)
- `meta_json` (json, optional)
- `status` (`draft` / `published`)
- `published_at` (datetime, optional)
- `created_by`, `updated_by` (FK to users, optional)
- timestamps

Model: `App\Models\Post`

- uses `HasMedia` trait
- scope `published()` for public visibility
- helper methods: `meta()`, `url()`, `isPublished()`

## Admin CRUD

- Controller: `App\Http\Controllers\Admin\PostController`
- Requests:
  - `StorePostRequest`
  - `UpdatePostRequest`
- Views:
  - `resources/views/admin/posts/index.blade.php`
  - `resources/views/admin/posts/create.blade.php`
  - `resources/views/admin/posts/edit.blade.php`
  - `resources/views/admin/posts/_form.blade.php`

Routes (admin):

```php
Route::resource('posts', PostController::class)->except(['show']);
```

## Public Frontend

- Controller: `App\Http\Controllers\BlogController`
- Views:
  - `resources/views/blog/index.blade.php`
  - `resources/views/blog/show.blade.php`

Routes (public):

```php
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
```

## SEO + Media

- SEO uses `meta_json` with the same keys used by pages (`description`, `og_title`, `og_description`)
- featured image uses existing polymorphic media relation with collection key `featured_image`

## Permissions

`PostPolicy`:

- `admin`: full CRUD
- `editor`: create/update/view
- `editor` cannot delete

This keeps editorial flexibility while preserving destructive actions for admins.

## How to create another collection

1. Create migration + model with `status` and `published_at`.
2. Add admin requests, controller, and views following `posts` structure.
3. Add public controller + routes before page catch-all routes.
4. Reuse media selector and SEO meta shape.
5. Add policy and feature tests for role boundaries.
