# Live Editor Engine

The live editor is the core editorial UX of Flaxt CMS. Pages, posts, and future collections should use the same editor shell and runtime behavior.

---

## Architecture

```
resources/views/admin/layouts/editor.blade.php    ← shared shell (top bar, edit pane, preview pane, viewports, toast)
resources/js/editor-engine.js                      ← shared runtime (dirty state, draft storage, preview, async save)
resources/views/admin/partials/_editor-footer.blade.php ← shared footer actions
```

The `edit.blade.php` for each content type should only provide:
- form include
- optional top-right actions (e.g. "view live")
- editor engine config (preview URL + draft key + messages)

---

## `initEditorEngine(config)`

Export: `initEditorEngine(config)`

Config:

| Parameter | Type | Description |
|-----------|------|-------------|
| `previewUrl` | `string` | POST endpoint returning rendered HTML preview |
| `draftKey` | `string` | localStorage key for editor draft |
| `savedMsg` | `string` | Toast message for successful save |
| `errorMsg` | `string` | Toast message for failed save |

Behavior:
1. Builds form snapshots (ignores `_token` and `_method`).
2. Computes dirty state and enables/disables save controls.
3. Saves/restores drafts in localStorage.
4. Refreshes preview with debounced POST (600ms).
5. Saves asynchronously with `Accept: application/json`.
6. Supports `status` override for draft/publish buttons.
7. Preserves iframe scroll position across preview refreshes.

DOM contract:
- required: `#editor-form`, `#save-btn`
- required when preview is enabled: `#preview-frame`
- optional footer controls: `#update-btn`, `#save-draft-btn`, `#publish-btn`

---

## Shared Footer Partial

Path: `resources/views/admin/partials/_editor-footer.blade.php`

Usage:

```blade
@section('editor-footer')
    @include('admin.partials._editor-footer', ['model' => $page])
@endsection
```

Requirements:
- `$model->isPublished()` must exist.

Output:
- published content: one `Save changes` button (`#update-btn`)
- draft content: `Save draft` (`#save-draft-btn`) and `Publish` (`#publish-btn`)

---

## Preview Viewports

The shared editor layout provides three preview modes:
- `desktop`
- `tablet`
- `mobile`

Switching mode updates iframe dimensions in-place:
- desktop: full available width/height
- tablet: fixed medium portrait frame
- mobile: fixed narrow portrait frame

Because viewports live in `admin.layouts.editor`, pages/posts/collections inherit them automatically.

---

## Pages Content UX: Shared Sections + Accordions

`admin/pages/_form.blade.php` now treats template sections as shared keys:
- one rendered block per section key
- section block tagged with all template keys using `data-templates`
- template switch only shows blocks that include the selected template key

This avoids duplicate inputs when switching between templates that share the same sections (e.g. `home` and `home-alt`).

Content sections in the `Content` tab are rendered as accordions:
- all sections start collapsed
- multiple sections can stay open at the same time
- collapsed row shows only:
  - section title
  - visibility switch

If section-specific partials are used in the future (`admin/pages/sections/{template}_{section}.blade.php`), the form resolves a fallback partial from the first template that provides that section partial.

---

## Backend Contract

The editor runtime is frontend-only. Backend requirements per content type:
1. `previewRender(Request $request, Model $model): Response` that returns raw HTML.
2. `update()` must return JSON when `$request->wantsJson()`.
3. A preview POST route (e.g. `admin.pages.preview`, `admin.posts.preview`).

No database changes are required for the live editor engine itself.
