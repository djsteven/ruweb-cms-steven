<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaJsonSerializationTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_show_response_includes_computed_url_for_selector_preview(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $media = Media::create([
            'filename' => 'example.jpg',
            'original_filename' => 'example.jpg',
            'path' => 'media/2026/04/example.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => 12345,
            'alt' => 'Example image',
            'title' => 'Example',
            'disk' => 'public',
            'uploaded_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.media.show', $media));

        $response->assertOk();
        $response->assertJsonPath('id', $media->id);
        $response->assertJsonPath('url', $media->url());
        $response->assertJsonPath('is_image', true);
    }
}
