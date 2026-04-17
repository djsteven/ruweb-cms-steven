<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminTranslationsTest extends TestCase
{
    public function test_homepage_settings_translation_keys_exist_in_spanish_and_english(): void
    {
        $keys = [
            'admin.settings_fields.homepage_slug.label',
            'admin.settings_fields.homepage_slug.help',
            'admin.settings_fields.homepage_slug.empty',
            'admin.analytics',
            'admin.analytics_subtitle',
            'admin.analytics_google_title',
            'admin.analytics_meta_title',
            'admin.analytics_search_console_title',
            'admin.analytics_validation.google_tag_id',
            'admin.analytics_validation.meta_pixel_id',
            'admin.analytics_validation.search_console_verification_token',
        ];

        foreach ($keys as $key) {
            $this->assertTrue(trans()->has($key, 'es'), "Missing es translation: {$key}");
            $this->assertTrue(trans()->has($key, 'en'), "Missing en translation: {$key}");
        }
    }
}
