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
            ['key' => 'site_description',       'value' => 'A lightweight and extensible CMS starter kit.', 'type' => 'text',   'group' => 'general', 'options' => null],
            ['key' => 'site_logo',              'value' => null,               'type' => 'media',  'group' => 'general', 'options' => null],
            ['key' => 'site_favicon',           'value' => null,               'type' => 'media',  'group' => 'general', 'options' => null],
            ['key' => 'homepage_slug',          'value' => 'inicio',           'type' => 'select', 'group' => 'general', 'options' => []],
            ['key' => 'footer_text',            'value' => 'Built with FlaxtCMS', 'type' => 'text',   'group' => 'general', 'options' => null],
            ['key' => 'social_facebook',        'value' => '',                 'type' => 'string', 'group' => 'social',  'options' => null],
            ['key' => 'social_twitter',         'value' => '',                 'type' => 'string', 'group' => 'social',  'options' => null],
            ['key' => 'social_instagram',       'value' => '',                 'type' => 'string', 'group' => 'social',  'options' => null],
            ['key' => 'default_meta_title',     'value' => null, 'type' => 'string', 'group' => 'seo',     'options' => null],
            ['key' => 'default_meta_description','value' => 'A lightweight and extensible CMS starter kit.', 'type' => 'text',   'group' => 'seo',     'options' => null],
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
