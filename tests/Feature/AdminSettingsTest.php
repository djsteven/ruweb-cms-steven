<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Support\AdminLoginPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_settings_show_login_url_field(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.settings.index', ['tab' => 'admin']));

        $response->assertOk();
        $response->assertSee('URL de login');
    }

    public function test_admin_can_customize_login_path(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'settings' => [
                'admin_login_path' => 'acceso-seguro',
            ],
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertSame('acceso-seguro', Setting::get('admin_login_path'));
        AdminLoginPath::clearCache();
        Auth::logout();

        $this->get('/admin/login')->assertNotFound();
        $this->get('/admin/acceso-seguro')->assertOk();
    }

    public function test_admin_login_path_rejects_reserved_segments(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->from(route('admin.settings.index'))->put(route('admin.settings.update'), [
            'settings' => [
                'admin_login_path' => 'posts',
            ],
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHasErrors('settings.admin_login_path');
    }

    public function test_admin_login_path_rejects_reserved_segments_case_insensitively(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->from(route('admin.settings.index'))->put(route('admin.settings.update'), [
            'settings' => [
                'admin_login_path' => 'Posts',
            ],
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHasErrors('settings.admin_login_path');
        $this->assertSame('login', Setting::get('admin_login_path', 'login'));
    }
}
