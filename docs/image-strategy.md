# Image Strategy

This guide defines the framework-level image optimization and responsive delivery strategy.

## Goals

- Convert eligible image uploads (`jpg`, `jpeg`, `png`) to WebP.
- Preserve exact traceability for savings per asset.
- Generate responsive variants for eligible images.
- Expose live media health metrics from DB + storage.

## Configuration

Config keys live in [`config/cms.php`](/Users/alemanydev/Developer/flaxt-cms/config/cms.php):

- `cms.image_optimization.enabled` (default: `true`)
- `cms.image_optimization.max_width` (default: `2048`)
- `cms.image_optimization.quality` (default: `80`)
- `cms.image_optimization.keep_original` (default: `false`)
- `cms.responsive_images.enabled` (default: `true`)
- `cms.responsive_images.widths` (default: `[240,480,768,1024,1536]`)

## Upload pipeline

When an eligible image is uploaded:

1. Original metadata is captured (`original_size`, `original_extension`, `original_mime_type`).
2. The file is optimized to WebP using GD.
3. The media record is updated with final file info (`path`, `filename`, `extension`, `mime_type`, `size`, `width`, `height`, `optimized_at`).
4. Responsive variants are generated and stored in `variants` JSON.
5. Original file handling:
   - `keep_original=false`: original physical file is deleted and `original_path` stays `null`.
   - `keep_original=true`: original file is moved under `originals/` and `original_path` is persisted.

`svg`, `gif`, and non-image documents are never converted and never get responsive variants.

## Traceability model

Media optimization uses per-asset fields in `media`:

- `size`: final optimized asset size.
- `original_size`: pre-optimization bytes.
- `original_extension`, `original_mime_type`: original format metadata.
- `original_path`: only when original is physically preserved.
- `width`, `height`: final image dimensions.
- `variants`: responsive variants metadata.
- `optimized_at`: optimization timestamp.

The model exposes reusable helpers:

- `Media::srcset()`
- `Media::hasResponsiveVariants()`
- `Media::bytesSaved()`
- `Media::optimizationRatio()`
- `Media::isOptimizedRaster()`

## Frontend rendering

Use [`resources/views/components/responsive-img.blade.php`](/Users/alemanydev/Developer/flaxt-cms/resources/views/components/responsive-img.blade.php) for public image rendering.

- It renders `srcset` and `sizes` when variants exist.
- It falls back to normal `src` when variants are unavailable.
- Alt priority: explicit `alt` prop, then `media.alt`, then `fallbackAlt`.

## Operations

Run in this order:

```bash
php artisan media:convert-webp
php artisan media:generate-variants
```

Never run both commands in parallel.

Available commands:

- `php artisan media:convert-webp {--dry-run}`
- `php artisan media:generate-variants {--dry-run} {--force}`
- `php artisan media:audit-health`

## Dashboard and health view

Admin dashboard includes a media health summary block with:

- total media
- optimizable images
- WebP coverage %
- responsive coverage %
- bytes saved
- missing physical files

The dedicated `Media Health` admin page adds:

- total optimizable images
- total optimized
- total with variants
- total saved MB
- average reduction ratio
- preserved originals
- missing physical files
- missing dimensions
- missing variants
- top assets by savings
- top pending optimization

All metrics are calculated live from DB + storage state.
