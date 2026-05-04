<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProfilePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_uses_information_and_security_tabs_without_mcp_api_key(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get(route('admin.profile.index'));

        $response->assertOk();
        $response->assertSee(__('admin.profile_tab_information'));
        $response->assertSee(__('admin.profile_tab_security'));
        $response->assertSee(__('admin.field_role'));
        $response->assertDontSee(__('admin.mcp_api_key'));
    }

    public function test_claude_mcp_page_contains_mcp_api_key_management(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get(route('admin.claude-mcp.index'));

        $response->assertOk();
        $response->assertSee(__('admin.mcp_api_key'));
        $response->assertSee(__('admin.mcp_api_key_generate'));
    }
}
