# Pages Guide

This document explains how pages work in Flaxt CMS and the rules that govern creating and editing them. Pages are the core content unit of the system — every URL that is not a collection entry resolves to a page.

---

## How a page works

A page is a record in the `pages` table with:

- A unique `slug` that maps to a public URL (`/about`, `/services/web`, etc.)
- A `template_key` that selects which Blade template renders it
- A `content_json` column that holds all editable content (meta and sections)
- A `status` (`draft` / `published`) and `published_at` timestamp
- A featured image via the `mediables` polymorphic relationship

The public router resolves any URL against published pages by slug. This is a catch-all route — **all collection routes must be registered before it.**

---

## Mandatory rules

### 1. Never hardcode images

Every image on a page must be uploaded to the media library and referenced through the `mediables` relationship. This applies to featured images, section images, and any image displayed on the public frontend.

Forbidden:

```blade
{{-- NEVER --}}
<img src="/images/hero.jpg" alt="Hero">
<img src="{{ asset('img/team-photo.png') }}" alt="Team">
```

Correct — featured image:

```blade
@if($page->featuredImage())
    <img
        src="{{ $page->featuredImage()->url() }}"
        alt="{{ $page->featuredImage()->alt ?: $page->title }}"
    >
@endif
```

Correct — image stored as a media ID inside a section:

```blade
@php
    $hero = $page->sections()['hero'] ?? [];
    $heroImage = isset($hero['image_id']) ? \App\Models\Media::find($hero['image_id']) : null;
@endphp

@if($heroImage)
    <img src="{{ $heroImage->url() }}" alt="{{ $heroImage->alt }}">
@endif
```

When a template section needs an image field, store the `media_id` inside `content_json.sections.{section}.image_id` — not the URL or path.

### 2. Image placeholders are pastel fills — never text or borders

When a section or component renders an image and no media has been set yet, the placeholder **must** be a solid pastel color fill. No placeholder text, no dashed/solid borders, no icons, no labels.

This keeps the visual language consistent with the shortcut cards, slider fallbacks, and other areas of the site that already use the shared pastel palette.

Required pattern:

```blade
@php
    $pastelColors = ['#F8B4B4', '#A7C7E7', '#B5EAD7', '#FFDAC1', '#C3B1E1', '#FFE5B4'];
    $sectionMedia = isset($section['image_id']) ? \App\Models\Media::find($section['image_id']) : null;
    $placeholderColor = $pastelColors[array_rand($pastelColors)];
@endphp

@if($sectionMedia)
    <img src="{{ $sectionMedia->url() }}" alt="{{ $sectionMedia->alt }}">
@else
    <div style="background-color: {{ $placeholderColor }};" class="w-full h-full"></div>
@endif
```

Rules:
- Use `array_rand($pastelColors)` (or a deterministic index like `$idx % count($pastelColors)`) to pick a color.
- No text content inside the placeholder `div`.
- No CSS borders on the placeholder element.
- Size and shape come from the parent container, not the placeholder itself.

Forbidden:

```blade
{{-- NEVER --}}
<div class="border border-gray-300 flex items-center justify-center">
    <span>[Foto estacion]</span>
</div>
```

### 3. Featured image uses HasMedia — not content_json

The featured image is **not** stored inside `content_json`. It is attached via the polymorphic `mediables` pivot with the collection key `featured_image`.

```php
// Attach on store
$page->attachMedia($mediaId, 'featured_image');

// Replace on update — always detach first
$page->media()->wherePivot('collection', 'featured_image')->detach();
if ($featuredImage) {
    $page->attachMedia($featuredImage, 'featured_image');
}

// Access in Blade
$page->featuredImage()   // returns Media|null
```

Validate in FormRequest as:

```php
'featured_image' => ['nullable', 'integer', 'exists:media,id'],
```

### 4. Every section must respect is_visible

Every section in a Blade template **must** gate its output behind `is_visible`. This field is already rendered as a toggle switch in the admin form for every section — the template must honor it.

Required pattern in every template:

```blade
@if(($hero['is_visible'] ?? 1) && ($hero['heading'] ?? null))
    <section>...</section>
@endif
```

Rules:
- Default to `1` (visible) when the field is absent — `?? 1` — so new sections show by default.
- Check `is_visible` **and** meaningful content in the same condition. Do not render empty sections.
- Never render a section that has `is_visible = 0`, even if it has content.

The toggle is already present in `admin/pages/_form.blade.php` for every section. No extra admin work is needed — this is purely a template-side obligation.

### 5. Content lives in content_json — two keys only

All editable page content is stored in `content_json` with exactly two top-level keys:

```json
{
    "meta": {
        "description": "...",
        "og_title": "...",
        "og_description": "..."
    },
    "sections": {
        "hero": { "heading": "...", "body": "..." },
        "cta":  { "heading": "...", "body": "..." }
    }
}
```

- `meta` — SEO and social sharing metadata. Never add layout or design data here.
- `sections` — content blocks defined by the template. Keys must match the `sections` array in `config/cms.php` for the chosen template.

Do not add a third top-level key. Do not store media URLs inside `content_json`.

### 6. The edit view must use the live editor layout

Every page uses the live editor — `edit.blade.php` must extend `admin.layouts.editor`, use the `_editor-footer` partial, and call `initEditorEngine()`. The create view uses `admin.layouts.app`.

> Full details on the editor engine, DOM contract, and layout variables: see `docs/live-editor.md`.

In the `Content` tab, section blocks are accordion cards:
- collapsed by default
- multiple sections can be open at once
- collapsed header only shows section title + `Visible` switch

The layout-level preview also includes three shared viewports: desktop, tablet, and mobile.

The `previewRender` method mutates the page in memory and renders the resolved template without saving:

```php
public function previewRender(Request $request, Page $page): Response
{
    $page->title        = $request->input('title', $page->title);
    $page->template_key = $request->input('template_key', $page->template_key);
    $page->content_json = $request->input('content_json', $page->content_json ?? []);

    $html = view($page->resolveTemplate(), ['page' => $page])->render();

    return response($html)->header('Content-Type', 'text/html');
}
```

> **Nota (backend):** Cuando un modelo guarda un bloque JSON flexible como `content_json`, no usar `$request->validated()` como fuente final de persistencia del árbol completo si existen reglas parciales para subclaves anidadas. Validar la request, pero persistir `content_json` desde `$request->input('content_json', ...)`, o definir una estrategia de saneamiento explícita que reconstruya el árbol completo.

The `update` method must return JSON when `$request->wantsJson()` (the editor saves via `fetch`):

```php
if ($request->wantsJson()) {
    return response()->json(['saved' => true]);
}
```

---

## Template system

Each page selects a template via `template_key`. Templates live at `resources/views/templates/{key}.blade.php` and are registered in `config/cms.php`.

### Registering a new template

```php
// config/cms.php
'templates' => [
    'default' => [
        'name'     => 'Default',
        'sections' => ['hero', 'body'],
    ],
    'landing' => [
        'name'     => 'Landing Page',
        'sections' => ['hero', 'features', 'testimonials', 'cta'],
    ],
],
```

The `sections` array drives what fields appear in the admin form. Every key listed here gets a `heading` and `body` field by default in `_form.blade.php`.

The pages editor deduplicates section inputs by key across templates:
- if two templates share `hero`, the editor renders one `hero` block tagged for both templates
- switching template keeps the same inputs and current values active
- this prevents preview/editor data loss when toggling between compatible templates (e.g. `home` and `home-alt`)

For template-specific section UI, optional partials can be placed at:
`resources/views/admin/pages/sections/{template}_{section}.blade.php`

If a selected template does not have its own partial, the editor falls back to another template partial that shares that section key.

### Template Blade file

```blade
{{-- resources/views/templates/landing.blade.php --}}
@extends('layouts.public')

@section('content')
@php
    $sections = $page->sections();
    $hero     = $sections['hero'] ?? [];
    $cta      = $sections['cta'] ?? [];
@endphp

@if(($hero['is_visible'] ?? 1) && ($hero['heading'] ?? null))
<section>
    <h1>{{ $hero['heading'] }}</h1>
    @if($hero['body'] ?? null)
        <p>{{ $hero['body'] }}</p>
    @endif
</section>
@endif

@if(($cta['is_visible'] ?? 1) && ($cta['heading'] ?? $cta['body'] ?? null))
<section>
    {{-- cta content --}}
</section>
@endif

@endsection
```

Available variables in every template:

| Variable | Type | Description |
|----------|------|-------------|
| `$page->title` | string | The page title (DB column) |
| `$page->meta()` | array | `content_json.meta` block |
| `$page->sections()` | array | `content_json.sections` block |
| `$page->featuredImage()` | `Media\|null` | Featured image via mediables |
| `$page->url()` | string | Public URL (`/slug`) |

If `template_key` has no matching Blade view, the system falls back to `templates.default`.

---

## Checklist — adding a new page

Pages are data, not code. Creating a new page means adding a record in the admin — no code changes needed unless a new template is required.

### If the page fits an existing template

1. Go to `/admin/pages/create`.
2. Fill in title, slug, and select the template.
3. Upload the featured image via the media selector (do not paste URLs).
4. Fill in section fields.
5. Save as draft or publish.

### If the page needs a new template

1. **Register in config** — add the template key and its sections to `config/cms.php`.
2. **Create the Blade file** — `resources/views/templates/{key}.blade.php`.
3. **Access content** using `$page->sections()`, `$page->meta()`, `$page->featuredImage()`.
4. **Do not store image URLs in sections** — use `image_id` referencing a media record, or attach extra media via `mediaByCollection('your-key')`.
5. Create the page in the admin as above.

---

## SEO metadata

The `meta` block inside `content_json` feeds the `seo-meta` Blade component. The fallback chain is:

- **Title**: `og_title` → page `title` → `default_meta_title` setting → `app.name`
- **Description**: `og_description` → `description` → `default_meta_description` setting
- **Image**: `$page->featuredImage()->url()` (via mediables, not from content_json)

Only fill `og_title` / `og_description` when you need values different from the base title and description.

---

## Quick reference

| Concern | How | Never |
|---------|-----|-------|
| Featured image | `HasMedia` → `mediables` pivot, `featured_image` collection | Store URL/path in `content_json` |
| Section images | Store `image_id` in section, resolve with `Media::find()` | Hardcode paths or `asset()` calls |
| Page content | `content_json` with `meta` + `sections` keys | Add a third top-level key |
| New template | Register in `config/cms.php` + create Blade file | Create pages without a registered template |
| Section visibility | `($section['is_visible'] ?? 1)` gate on every section | Render sections ignoring the toggle |
| Edit view | `admin.layouts.editor` with live preview | `admin.layouts.app` on the edit view |
| Drafts visibility | `->published()` scope on all public queries | Expose pages without scope |
