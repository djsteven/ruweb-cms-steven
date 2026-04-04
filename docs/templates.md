# Templates

## How Templates Work

Each page has a `template_key` that maps to a Blade view at `resources/views/templates/{key}.blade.php`. Templates define the visual structure and which sections of `content_json` they render.

## Creating a New Template

### 1. Register in config

Add your template to `config/cms.php`:

```php
'templates' => [
    // ...existing templates...
    'landing' => [
        'name' => 'Landing Page',
        'sections' => ['hero', 'benefits', 'testimonials', 'cta'],
    ],
],
```

The `sections` array defines which content sections appear in the admin form for this template.

### 2. Create the Blade view

Create `resources/views/templates/landing.blade.php`:

```blade
@extends('layouts.public')

@section('content')
@php
    $sections = $page->sections();
    $hero = $sections['hero'] ?? [];
    $benefits = $sections['benefits'] ?? [];
    $testimonials = $sections['testimonials'] ?? [];
    $cta = $sections['cta'] ?? [];
@endphp

{{-- Hero --}}
@if($hero['heading'] ?? null)
<section class="py-20">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold">{{ $hero['heading'] }}</h1>
        @if($hero['body'] ?? null)
            <p class="mt-6 text-xl text-gray-600">{{ $hero['body'] }}</p>
        @endif
    </div>
</section>
@endif

{{-- Add more sections as needed --}}
@endsection
```

### 3. Use it

When creating or editing a page in the admin, select your new template from the dropdown. The form will show the section fields you defined in the config.

## Template Data

Every template receives a `$page` variable (the `Page` model instance). Access content via:

- `$page->title` — The page title
- `$page->meta()` — Returns the `meta` block from `content_json` (description, og_title, og_description, featured_image)
- `$page->sections()` — Returns the `sections` block from `content_json`
- `$page->featuredImage()` — Returns the featured `Media` model (via HasMedia trait)
- `$page->url()` — Returns the public URL

## Section Fields

In the admin, each section renders two default fields: **heading** and **body**. These are stored in `content_json.sections.{section_name}.heading` and `content_json.sections.{section_name}.body`.

To add custom fields per section, modify the `_form.blade.php` partial or create a custom form for your template.

## Fallback

If a page references a `template_key` that has no corresponding Blade view, it falls back to `templates.default`.
