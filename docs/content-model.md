# Content Model

## Purpose

This document defines the canonical shape of structured content stored in a JSON column such as `content_json`. It is the source of truth for data shape only.

Use this document to understand:

- which top-level keys exist
- which fields are reserved
- how section payloads are organized

Do not use this document for template registration, page lifecycle, or editor behavior. Those topics live in `docs/templates.md`, `docs/pages-guide.md`, and `docs/live-editor.md`.

## Canonical Shape

```json
{
    "meta": {
        "title": "Optional page title override",
        "description": "Page description for search engines",
        "og_title": "Optional social sharing title override",
        "og_description": "Optional social sharing description override",
        "featured_image": 42
    },
    "sections": {
        "hero": {
            "is_visible": 1,
            "heading": "Welcome",
            "body": "A short introduction."
        },
        "features": {
            "is_visible": 0,
            "heading": "What we offer",
            "body": "Feature descriptions."
        }
    }
}
```

## Top-Level Keys

Only two top-level keys are part of the standard structured content contract:

- `meta`
- `sections`

Do not introduce additional top-level keys unless the project intentionally defines a different contract and documents it separately.

For multilingual sites, each localized entity has its own `content_json`. Do not store values like `{ "es": "...", "en": "..." }` inside one JSON field. Translation behavior is described by the editorial schema in `docs/multilanguage.md`; the JSON column remains the storage format.

## Meta Block

The `meta` block stores SEO and social-sharing values.

| Field | Purpose | Typical fallback |
|-------|---------|------------------|
| `title` | Optional title override for meta output | Entity title or site name |
| `description` | Meta description tag | Global site description setting |
| `og_title` | Open Graph / social title override | Entity title |
| `og_description` | Open Graph / social description override | `description` |
| `featured_image` | Optional social image media identifier | Global default social image setting |

Notes:

- The primary title should remain in the entity's first-class column such as `title`; use `meta.title` only when the SEO/browser title intentionally differs.
- `og_title` should be populated only when the social title differs from the main title.
- Featured or social images should be resolved through the media system. Store media identifiers, not raw URLs.
- Entity-level featured images should stay in the shared media relationship. `meta.featured_image` is for an explicit social image override.

## Sections Block

The `sections` object stores all editable presentation content keyed by logical section name.

Example:

```json
{
    "sections": {
        "hero": {
            "is_visible": 1,
            "heading": "Welcome",
            "body": "Short introduction",
            "image_id": 42
        },
        "faq": {
            "is_visible": 1,
            "heading": "Frequently asked questions",
            "items": [
                { "question": "Question A", "answer": "Answer A" }
            ]
        }
    }
}
```

Rules:

- Each section key must match a section expected by the selected template.
- Each section should include `is_visible`.
- Additional keys are allowed inside a section when required by the template.
- Media references inside sections should store media identifiers such as `image_id`, not file paths or external URLs.

## Visibility Contract

`is_visible` is the standard section visibility flag.

- `1` means the section may render.
- `0` means the section must not render.
- If a section omits the field, templates should usually treat it as visible by default.

## Access Pattern

Typical server-side access:

```php
$meta = $entity->meta();
$sections = $entity->sections();
$hero = $sections['hero'] ?? [];
```

Typical template guard:

```blade
@if(($hero['is_visible'] ?? 1) && ($hero['heading'] ?? null))
    <section>...</section>
@endif
```

## Non-Goals

This document does not define:

- how templates are registered
- how page routes are resolved
- how the admin editor works
- how collections implement first-class columns such as `excerpt` or `content`
