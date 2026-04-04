# Content Model

## Overview

Each page stores its content in a `content_json` column (MySQL JSON type). This column has two top-level keys: `meta` and `sections`.

## Structure

```json
{
    "meta": {
        "description": "Page description for search engines",
        "og_title": "Optional override for social sharing title",
        "og_description": "Optional override for social sharing description"
    },
    "sections": {
        "hero": {
            "is_visible": 1,
            "heading": "Welcome to our site",
            "body": "A brief introduction..."
        },
        "features": {
            "is_visible": 0,
            "heading": "What we offer",
            "body": "Feature descriptions..."
        }
    }
}
```

## Meta Block

The `meta` block unifies SEO and social sharing metadata:

| Field | Purpose | Fallback |
|-------|---------|----------|
| `description` | Meta description tag | `default_meta_description` setting |
| `og_title` | Open Graph / Twitter title | Page `title` field → `default_meta_title` setting |
| `og_description` | Open Graph / Twitter description | `description` → `default_meta_description` setting |

The page `title` (database column, not inside `content_json`) is the primary title. `og_title` only needs to be set when you want a different title for social sharing.

### Featured Image

The featured image is not stored inside `content_json`. It uses the polymorphic `mediables` relationship with collection `'featured_image'`. Access it via `$page->featuredImage()`.

## Sections Block

Each key in `sections` corresponds to a template section. The available sections are defined per template in `config/cms.php`:

```php
'templates' => [
    'home' => [
        'name' => 'Homepage',
        'sections' => ['hero', 'features', 'cta'],
    ],
],
```

Every section object has a mandatory `is_visible` field (integer `0` or `1`). The admin form renders a toggle switch for it automatically. Blade templates must always gate section output behind this flag:

```blade
@if(($hero['is_visible'] ?? 1) && ($hero['heading'] ?? null))
    <section>...</section>
@endif
```

Default to `1` when the field is absent (`?? 1`) so new sections appear by default.

Beyond `is_visible`, the default admin form provides `heading` and `body` fields per section. You can extend sections with any additional fields your template needs.

## SEO Fallback Chain

The `ContentHelper` class and `seo-meta` Blade component implement this fallback chain:

- **Title**: `og_title` → page `title` → `default_meta_title` setting → `app.name` config
- **Description**: `og_description` → `description` → `default_meta_description` setting
- **Image**: Featured image media URL (via `mediables` relationship)

## Accessing Content in Templates

```blade
{{-- In a Blade template --}}
@php
    $meta = $page->meta();
    $sections = $page->sections();
    $hero = $sections['hero'] ?? [];
@endphp

<h1>{{ $hero['heading'] ?? '' }}</h1>
<p>{{ $hero['body'] ?? '' }}</p>
```
