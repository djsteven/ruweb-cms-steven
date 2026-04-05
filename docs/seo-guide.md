# SEO Guide

This document covers SEO features built into Flaxt CMS and the conventions for keeping them working as the site grows.

---

## Sitemap XML

The CMS generates a dynamic sitemap at `/sitemap.xml`. It is built by `SitemapController` and cached for 60 minutes.

### What's included

| URL | Priority | Changefreq |
|-----|----------|------------|
| `/` (homepage) | 1.0 | daily |
| `/{slug}` (static pages) | 0.8 | weekly |
| `/blog` (blog index) | 0.6 | daily |
| `/blog/{slug}` (posts) | 0.5 | monthly |

Only **published** content appears (`status = 'published'` and `published_at <= now()`). Drafts are never included.

### Cache

The sitemap XML is cached under the key `sitemap-xml` for 3600 seconds (60 minutes). To invalidate immediately after bulk changes, run:

```php
Cache::forget('sitemap-xml');
```

or via Artisan:

```bash
php artisan cache:clear
```

---

## Adding a new collection to the sitemap

When you create a new collection (e.g., `products`), follow these two steps to include it in the sitemap.

### Prerequisites

Your model must have:
- A `published()` scope (see `collections-guide.md` — mandatory rule 4)
- A `slug` column
- An `updated_at` timestamp

### Step 1 — Add the query in `SitemapController`

Open `app/Http/Controllers/SitemapController.php` and add a query inside the `Cache::remember` closure:

```php
$products = \App\Models\Product::published()->latest('published_at')->get();
```

Pass it to the view:

```php
return view('sitemap.index', compact('homePage', 'pages', 'posts', 'latestPostDate', 'products'))->render();
```

### Step 2 — Add the URL block in the Blade view

Open `resources/views/sitemap/index.blade.php` and add a new block before the closing `</urlset>` tag:

```blade
{{-- Products --}}
@foreach($products as $product)
<url>
    <loc>{{ url('/products/' . $product->slug) }}</loc>
    <lastmod>{{ $product->updated_at->toW3cString() }}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
</url>
@endforeach
```

Adjust the URL prefix, `changefreq`, and `priority` to match the collection's content type.

### Priority conventions

Use these values as a guide:

| Content type | Priority | Changefreq |
|-------------|----------|------------|
| Homepage | 1.0 | daily |
| Top-level index pages | 0.8 | weekly |
| Section indexes (e.g. `/blog`) | 0.6 | daily |
| Individual detail pages | 0.5 | monthly |
| Archived or low-priority content | 0.3 | yearly |

---

## Meta tags (per-page SEO)

Every public-facing model uses `meta_json` to store per-item SEO overrides:

```json
{
    "description": "Page description",
    "og_title": "Social sharing title (optional)",
    "og_description": "Social sharing description (optional)"
}
```

These are rendered via `resources/views/partials/seo-meta.blade.php`. See `content-model.md` for the full implementation.

New collections must include a `meta_json` JSON column and a `meta(): array` accessor — see `collections-guide.md` rule 6.

---

## robots.txt

A `public/robots.txt` file controls crawler access. At minimum it should point to the sitemap:

```
User-agent: *
Disallow: /admin/

Sitemap: https://yourdomain.com/sitemap.xml
```

Update the sitemap URL to match `APP_URL` before going live.

---

## Checklist for a new collection

- [ ] Model has `published()` scope and `slug` column
- [ ] Public route registered before the `/{slug}` catch-all in `web.php`
- [ ] Query added to `SitemapController`
- [ ] URL block added to `sitemap/index.blade.php`
- [ ] `meta_json` column + `meta()` accessor present on the model
- [ ] `robots.txt` does not accidentally block the collection's URLs
