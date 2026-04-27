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

These are rendered via `resources/views/components/seo-meta.blade.php`. The component also applies global site-level fallbacks from `settings`, including:

- `site_name` — fallback page title and `og:site_name`
- `site_description` — fallback meta description and `og:description`
- `default_social_image` — fallback Open Graph / Twitter share image
- `site_favicon` — favicon
- canonical URL
- Full Open Graph and Twitter card tags

See `content-model.md` for the full implementation.

New collections must include a `meta_json` JSON column and a `meta(): array` accessor — see `collections-guide.md` rule 6.

---

## Analytics and verification tags

The public SEO/meta component is also the centralized place where browser-side analytics and Search Console verification tags are injected when configured from the admin.

### Admin-managed integrations

The **Admin → Analytics** screen supports three values:

- `google_tag_id`
- `meta_pixel_id`
- `search_console_verification_token`

The CMS stores only normalized IDs/tokens and generates the tags itself. It does **not** store or execute arbitrary pasted HTML/JavaScript snippets.

### What gets rendered

If configured, the public site renders:

- **Google tag** in the public `<head>`
- **Meta Pixel** base script in the public `<head>`
- **Meta Pixel** `noscript` fallback in the public `<body>`
- **Search Console** verification meta tag in the public `<head>`

These integrations are rendered only on the public site layout. Admin pages do not include them.

### Search Console guidance

The Search Console field in the admin expects only the verification token from the HTML tag method, not the full `<meta>` tag. The CMS generates:

```html
<meta name="google-site-verification" content="YOUR_TOKEN" />
```

This is intended for **URL-prefix** verification.

For broader ownership coverage across subdomains and protocol variants, prefer a **Domain property** verified through DNS. The admin UI includes a short DNS reminder, but DNS records are still managed outside the CMS.

### Current scope and limitations

- Google support is limited to the base Google tag bootstrap generated from the saved ID.
- Meta support is limited to the base browser-side Meta Pixel bootstrap plus `PageView`.
- Search Console support is limited to HTML-tag verification token injection.
- Meta Conversions API / server-side events are **not** part of the current implementation.

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
- [ ] Review whether the collection needs any custom analytics or verification behavior beyond the global tags
- [ ] `robots.txt` does not accidentally block the collection's URLs
