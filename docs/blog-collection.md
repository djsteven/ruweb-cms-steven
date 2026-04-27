# Blog Collection Reference

## Purpose

This document is an example implementation of a collection. It is not the source of truth for collection rules.

Use this document when you want to inspect how a concrete collection maps the generic rules from `docs/collections-guide.md` into actual files and routes.

## What This Example Demonstrates

- a collection with first-class content fields such as `excerpt` and `content`
- publication state through `status` and `published_at`
- optional metadata for SEO
- shared media usage for featured images
- shared taxonomy usage for categories
- a public index route and a public detail route

For the shared contracts behind these behaviors, see `docs/editorial-contract.md`.

## Typical Domain Shape

Example fields:

- `title`
- `slug`
- `excerpt`
- `content`
- `meta_json`
- `status`
- `published_at`
- audit fields such as `created_by` and `updated_by`

`Post` keeps these first-class fields while implementing the shared editorial contracts for publishing, SEO, media, and preview.

## Typical Application Pieces

- model: `App\Models\Post`
- admin controller: `App\Http\Controllers\Admin\PostController`
- public controller: `App\Http\Controllers\BlogController`
- admin views under `resources/views/admin/posts/`
- public views under `resources/views/blog/`
- category terms through `App\Models\Taxonomy` with type `category`

## Typical Routes

Admin:

```php
Route::resource('posts', PostController::class)->except(['show']);
```

Public:

```php
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
```

## Why This Stays As An Example

This document exists to show one complete reference implementation, but it should not duplicate:

- generic collection rules
- taxonomy rules
- editor engine internals
- deployment or project-specific content workflows
