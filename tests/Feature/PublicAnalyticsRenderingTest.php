<?php

namespace Tests\Feature;

use App\Models\Setting;
use Database\Seeders\HomepageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAnalyticsRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_no_analytics_when_not_configured(): void
    {
        $this->seed(SettingsSeeder::class);
        $this->seed(HomepageSeeder::class);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('googletagmanager.com/gtag/js');
        $response->assertDontSee('connect.facebook.net/en_US/fbevents.js');
        $response->assertDontSee('google-site-verification');
    }

    public function test_public_pages_render_all_configured_integrations(): void
    {
        $this->seed(SettingsSeeder::class);
        $this->seed(HomepageSeeder::class);

        Setting::set('google_tag_id', 'G-TEST123456');
        Setting::set('meta_pixel_id', '123456789012345');
        Setting::set('search_console_verification_token', 'abc123_DEF-456');
        Setting::clearCache();

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TEST123456', false);
        $response->assertSee("gtag('config', 'G-TEST123456');", false);
        $response->assertSee("fbq('init', '123456789012345');", false);
        $response->assertSee('https://www.facebook.com/tr?id=123456789012345&ev=PageView&noscript=1', false);
        $response->assertSee('<meta name="google-site-verification" content="abc123_DEF-456">', false);
    }

    public function test_admin_pages_do_not_render_public_analytics_snippets(): void
    {
        $this->seed(SettingsSeeder::class);

        Setting::set('google_tag_id', 'G-TEST123456');
        Setting::set('meta_pixel_id', '123456789012345');
        Setting::set('search_console_verification_token', 'abc123_DEF-456');
        Setting::clearCache();

        $response = $this->get(route('admin.login'));

        $response->assertOk();
        $response->assertDontSee('googletagmanager.com/gtag/js');
        $response->assertDontSee('connect.facebook.net/en_US/fbevents.js');
        $response->assertDontSee('google-site-verification');
    }
}
