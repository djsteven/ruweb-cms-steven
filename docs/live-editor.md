# Live Editor Engine

The live editor is the central editorial UX of Flaxt CMS. Every content type that supports editing (pages, posts, and future collections) uses the same shared layout and JavaScript engine.

---

## Architecture

```
admin.layouts.editor          ← Blade layout: top bar, panels, viewport switcher, toast
resources/js/editor-engine.js ← JS module: snapshot, dirty detection, drafts, preview, save
admin/partials/_editor-footer ← Blade partial: save/draft/publish buttons
```

The layout provides the shell (header, edit panel, preview iframe, mobile tabs, viewport controls). The engine provides all runtime behavior. The footer provides the action buttons. Each `edit.blade.php` only passes configuration — no logic is duplicated.

---

## editor-engine.js

Exported function: `initEditorEngine(config)`

### Config

| Parameter | Type | Description |
|-----------|------|-------------|
| `previewUrl` | string | POST endpoint that accepts form data and returns rendered HTML |
| `draftKey` | string | localStorage key for auto-saved drafts (e.g. `page-draft-42`) |
| `savedMsg` | string | Toast message on successful save |
| `errorMsg` | string | Toast message on failed save |

### What it does

1. **Form snapshots** — serializes all form fields (excluding `_token` and `_method`) into a string for dirty comparison.
2. **Dirty detection** — compares current form state against the last saved snapshot. Enables/disables save buttons accordingly.
3. **localStorage drafts** — on every form change, saves all field values to localStorage. Restores them on page load if a draft exists. Clears the draft on successful save.
4. **Live preview** — debounced (600ms after last change). POSTs the full form to `previewUrl`, receives HTML, and writes it to the preview iframe via `srcdoc`. Preserves scroll position across refreshes.
5. **Async save** — submits the form via `fetch` with `Accept: application/json`. Shows toast on success/error. Supports status override for draft/publish buttons.

### DOM contract

The engine expects these element IDs to exist in the page:

| ID | Required | Element |
|----|----------|---------|
| `editor-form` | Yes | The `<form>` element (must have an `action` attribute) |
| `preview-frame` | Yes | The preview `<iframe>` |
| `save-btn` | Yes | Header save button (in the layout) |
| `update-btn` | No | Footer update button (published items) |
| `save-draft-btn` | No | Footer save-as-draft button (draft items) |
| `publish-btn` | No | Footer publish button (draft items) |

These IDs are already provided by `admin.layouts.editor` and `admin.partials._editor-footer`.

### How it's loaded

The engine is imported in `resources/js/admin.js` and exposed globally:

```js
import { initEditorEngine } from './editor-engine.js';
window.initEditorEngine = initEditorEngine;
```

This means any inline `<script>` in a Blade `@push('scripts')` block can call it directly.

---

## _editor-footer partial

Located at `resources/views/admin/partials/_editor-footer.blade.php`.

### Usage

```blade
@section('editor-footer')
    @include('admin.partials._editor-footer', ['model' => $product])
@endsection
```

**Required:** `$model` must respond to `isPublished()` (returns `bool`).

- If published → shows a single "Save changes" button (`#update-btn`).
- If draft → shows "Save draft" (`#save-draft-btn`) + "Publish" (`#publish-btn`).

---

## Adding the live editor to a new collection

A complete `edit.blade.php` for a new collection looks like this:

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
    @include('admin.partials._editor-footer', ['model' => $product])
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initEditorEngine({
            previewUrl: '{{ route('admin.products.preview', $product) }}',
            draftKey:   'product-draft-{{ $product->id }}',
            savedMsg:   '{{ __('admin.saved_success') }}',
            errorMsg:   '{{ __('admin.save_error') }}',
        });
    });
</script>
@endpush
```

That's it — no JS logic to write or maintain.

### Backend requirements

The engine is frontend-only. The backend must provide:

1. **A `previewRender` controller method** — accepts POST with form data, mutates the model in memory, renders the public view, returns raw HTML.
2. **An `update` method that returns JSON** — when `$request->wantsJson()`, return `response()->json(['saved' => true])` instead of a redirect.
3. **A POST preview route** — e.g. `Route::post('products/{product}/preview', ...)->name('products.preview')`.
4. **A model with `isPublished()`** — the footer partial needs this to decide which buttons to show.

See `docs/collections-guide.md` for the full controller and route patterns.

---

## Layout variables

The editor layout (`admin.layouts.editor`) accepts these Blade variables:

| Variable | Default | Purpose |
|----------|---------|---------|
| `$editorBackHref` | `route('admin.pages.index')` | Back arrow link target |
| `$editorBackTitle` | `__('admin.back_to_pages')` | Back arrow tooltip |
| `$showPreview` | `true` | Set to `false` to hide the preview panel entirely |

---

## Scroll retention

When the preview refreshes, the engine captures `iframe.contentWindow.scrollY` before assigning `srcdoc`, then restores it via a one-time `load` event listener. This prevents the preview from jumping to the top on every keystroke.

---

## Section order — live editor must match the public template

The live editor renders sections in the order they first appear across all templates, iterating `config/cms.php` from top to bottom. If a section key (e.g. `banner`) is already used by an earlier template, it gets inserted into the rendered order at that earlier position — and any later template that also has it will show `banner` out of order in its editor.

**Rule: the section order in `config/cms.php` must match the visual order in the public template.**

This is enforced automatically — `_form.blade.php` always iterates the current page's template first before all others, so its sections are inserted in the correct order. But the config is still the source of truth:

```php
// BAD — banner listed last in the public template but appears 2nd in the editor
// because 'inicio' (registered earlier) already placed 'banner' at position 6
'about' => ['sections' => ['hero', 'counters', 'overview', 'mission', 'vision', 'values', 'banner']],

// GOOD — the order in the array matches top-to-bottom order on the public page
'about' => ['sections' => ['hero', 'counters', 'overview', 'mission', 'vision', 'values', 'banner']],
```

When adding a new template: lay out the sections in the same order the public template renders them. Do not put `banner` or any other shared key at an arbitrary position.

---

## Section key deduplication — avoid collisions across templates

The admin form (`admin/pages/_form.blade.php`) deduplicates section blocks by their **key name**. If two templates share the same section key (e.g. both declare `info` in their `sections` array), the form renders only **one** block for that key, tagged to both templates, using whichever partial was found first in the config iteration order.

This means:

- The partial shown is the one belonging to whichever template is registered **earlier** in `config/cms.php`.
- The second template's `{template}_{section}.blade.php` partial is **silently ignored**.

### Rule: use unique section keys per template

When two templates have conceptually different sections that happen to share a name, give each a distinct key.

```php
// BAD — both use 'info' but mean different things
'combustibles' => ['sections' => ['hero', 'info', 'fuel_types']],
'about'        => ['sections' => ['hero', 'info', 'mission']],   // ← 'info' collides

// GOOD — unique keys
'combustibles' => ['sections' => ['hero', 'info', 'fuel_types']],
'about'        => ['sections' => ['hero', 'overview', 'mission']], // ← no collision
```

Generic keys like `hero`, `banner`, `map` are safe to share **only when** their admin partial and data shape are identical across templates (e.g. the shared `_hero.blade.php` partial). If the section needs a different form, it needs a different key.
