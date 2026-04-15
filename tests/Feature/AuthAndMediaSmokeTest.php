<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthAndMediaSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_upload_media_file(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson(route('admin.media.store'), [
            'file' => UploadedFile::fake()->image('cover.jpg'),
            'title' => 'Cover',
            'alt' => 'Cover image',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('title', 'Cover');
        $this->assertDatabaseHas('media', [
            'title' => 'Cover',
            'uploaded_by' => $admin->id,
        ]);
    }

    public function test_admin_can_upload_multiple_media_files(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson(route('admin.media.store'), [
            'files' => [
                UploadedFile::fake()->image('cover-a.jpg'),
                UploadedFile::fake()->image('cover-b.jpg'),
            ],
            'title' => 'Bulk upload',
            'alt' => 'Bulk upload image',
        ]);

        $response->assertCreated();
        $response->assertJsonCount(2, 'data');
        $this->assertDatabaseCount('media', 2);
    }
}
