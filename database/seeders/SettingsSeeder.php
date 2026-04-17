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
            ['key' => 'mail_enabled',           'value' => '0',                'type' => 'boolean','group' => 'email',   'options' => null],
            ['key' => 'brevo_api_key',          'value' => null,               'type' => 'password','group' => 'email',  'options' => null],
            ['key' => 'mail_from_address',      'value' => null,               'type' => 'string', 'group' => 'email',   'options' => null],
            ['key' => 'mail_from_name',         'value' => null,               'type' => 'string', 'group' => 'email',   'options' => null],
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
