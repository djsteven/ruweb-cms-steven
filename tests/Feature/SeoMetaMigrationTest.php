<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetaMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_moves_og_fields_into_unified_seo_fields(): void
    {
        $page = Page::create([
            'title' => 'About',
            'slug' => 'about',
            'template_key' => 'default',
            'content_json' => [
                'meta' => [
                    'og_title' => 'About social title',
                    'og_description' => 'About social description',
                ],
            ],
            'status' => 'draft',
        ]);

        $post = Post::create([
            'title' => 'Launch',
            'slug' => 'launch',
            'meta_json' => [
                'og_title' => 'Launch social title',
                'og_description' => 'Launch social description',
            ],
            'status' => 'draft',
        ]);

        $this->runSeoNormalizationMigration();

        $page->refresh();
        $post->refresh();

        $this->assertSame('About social title', $page->meta()['title']);
        $this->assertSame('About social description', $page->meta()['description']);
        $this->assertArrayNotHasKey('og_title', $page->meta());
        $this->assertArrayNotHasKey('og_description', $page->meta());

        $this->assertSame('Launch social title', $post->meta()['title']);
        $this->assertSame('Launch social description', $post->meta()['description']);
        $this->assertArrayNotHasKey('og_title', $post->meta());
        $this->assertArrayNotHasKey('og_description', $post->meta());
    }

    public function test_migration_does_not_overwrite_existing_seo_fields(): void
    {
        $page = Page::create([
            'title' => 'About',
            'slug' => 'about',
            'template_key' => 'default',
            'content_json' => [
                'meta' => [
                    'title' => 'Canonical SEO title',
                    'description' => 'Canonical SEO description',
                    'og_title' => 'Legacy social title',
                    'og_description' => 'Legacy social description',
                ],
            ],
            'status' => 'draft',
        ]);

        $this->runSeoNormalizationMigration();
        $page->refresh();

        $this->assertSame('Canonical SEO title', $page->meta()['title']);
        $this->assertSame('Canonical SEO description', $page->meta()['description']);
        $this->assertArrayNotHasKey('og_title', $page->meta());
        $this->assertArrayNotHasKey('og_description', $page->meta());
    }

    private function runSeoNormalizationMigration(): void
    {
        $migration = require base_path('database/migrations/2026_05_17_000001_normalize_seo_meta_fields.php');
        $migration->up();
    }
}
