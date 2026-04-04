<?php

namespace App\Helpers;

use App\Models\Media;
use App\Models\Setting;

class ContentHelper
{
    public static function metaTitle(array $meta, ?string $default = null): string
    {
        $siteName = Setting::get('site_name') ?: config('app.name');

        return ($meta['og_title'] ?? null)
            ?: ($meta['title'] ?? null)
            ?: $default
            ?: (Setting::get('default_meta_title') ?: null)
            ?: $siteName;
    }

    public static function metaDescription(array $meta, ?string $default = null): string
    {
        return $meta['og_description']
            ?? $meta['description']
            ?? $default
            ?? Setting::get('default_meta_description')
            ?? '';
    }

    public static function metaImage(array $meta): ?string
    {
        $mediaId = $meta['featured_image'] ?? null;

        if (! $mediaId) {
            return null;
        }

        $media = Media::find($mediaId);

        return $media?->url();
    }
}
