<?php

namespace Tests\Feature;

use App\Models\Setting;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_seeder_sets_default_branding_values(): void
    {
        $this->seed(SettingsSeeder::class);

        $this->assertDatabaseHas('settings', [
            'key' => 'site_description',
            'value' => 'A lightweight and extensible CMS starter kit.',
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'site_favicon',
            'type' => 'media',
            'value' => null,
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'default_meta_description',
            'value' => 'A lightweight and extensible CMS starter kit.',
        ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'homepage_slug',
            'type' => 'select',
            'value' => 'inicio',
        ]);

        $this->assertNull(Setting::get('site_favicon'));
    }
}
