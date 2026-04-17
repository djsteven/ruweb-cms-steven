<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name',              'value' => 'Flaxt', 'type' => 'string', 'group' => 'general', 'options' => null],
            ['key' => 'site_description',       'value' => 'Un CMS ligero y extensible para comenzar rápido.', 'type' => 'text',   'group' => 'general', 'options' => null],
            ['key' => 'site_logo',              'value' => null,               'type' => 'media',  'group' => 'general', 'options' => null],
            ['key' => 'site_favicon',           'value' => null,               'type' => 'media',  'group' => 'general', 'options' => null],
            ['key' => 'homepage_slug',          'value' => 'inicio',           'type' => 'select', 'group' => 'general', 'options' => []],
            ['key' => 'default_meta_title',     'value' => null, 'type' => 'string', 'group' => 'seo',     'options' => null],
            ['key' => 'default_meta_description','value' => 'Un CMS ligero y extensible para comenzar rápido.', 'type' => 'text',   'group' => 'seo',     'options' => null],
            ['key' => 'google_tag_id',          'value' => null,               'type' => 'string', 'group' => 'analytics', 'options' => null],
            ['key' => 'meta_pixel_id',          'value' => null,               'type' => 'string', 'group' => 'analytics', 'options' => null],
            ['key' => 'search_console_verification_token', 'value' => null,    'type' => 'string', 'group' => 'analytics', 'options' => null],
            ['key' => 'admin_locale',           'value' => 'es',               'type' => 'select', 'group' => 'admin',   'options' => ['es' => 'Español', 'en' => 'English']],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        Setting::clearCache();
    }
}
