<?php

namespace Tests\Feature;

use App\Contracts\Editorial\Mediable;
use App\Contracts\Editorial\Previewable;
use App\Contracts\Editorial\Publishable;
use App\Contracts\Editorial\Seoable;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_and_posts_implement_editorial_contracts(): void
    {
        $page = new Page(['title' => 'About', 'slug' => 'about']);
        $post = new Post(['title' => 'Launch', 'slug' => 'launch']);

        foreach ([$page, $post] as $entity) {
            $this->assertInstanceOf(Publishable::class, $entity);
            $this->assertInstanceOf(Seoable::class, $entity);
            $this->assertInstanceOf(Mediable::class, $entity);
            $this->assertInstanceOf(Previewable::class, $entity);
        }
    }

    public function test_pages_keep_the_shared_publication_contract(): void
    {
        $published = Page::create([
            'title' => 'Published Page',
            'slug' => 'published-page',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $draft = Page::create([
            'title' => 'Draft Page',
            'slug' => 'draft-page',
            'status' => 'draft',
            'published_at' => now()->subHour(),
        ]);

        $future = Page::create([
            'title' => 'Future Page',
            'slug' => 'future-page',
            'status' => 'published',
            'published_at' => now()->addHour(),
        ]);

        $this->assertTrue($published->isPublished());
        $this->assertFalse($draft->isPublished());
        $this->assertFalse($future->isPublished());
        $this->assertEquals(['published-page'], Page::published()->pluck('slug')->all());
    }

    public function test_posts_keep_the_shared_publication_contract(): void
    {
        $published = Post::create([
            'title' => 'Published Post',
            'slug' => 'published-post',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $draft = Post::create([
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'status' => 'draft',
            'published_at' => now()->subHour(),
        ]);

        $future = Post::create([
            'title' => 'Future Post',
            'slug' => 'future-post',
            'status' => 'published',
            'published_at' => now()->addHour(),
        ]);

        $this->assertTrue($published->isPublished());
        $this->assertFalse($draft->isPublished());
        $this->assertFalse($future->isPublished());
        $this->assertEquals(['published-post'], Post::published()->pluck('slug')->all());
    }

    public function test_entities_expose_seo_metadata_from_their_existing_storage(): void
    {
        $page = new Page([
            'title' => 'About',
            'slug' => 'about',
            'content_json' => [
                'meta' => [
                    'description' => 'About page description',
                    'og_title' => 'About social title',
                ],
            ],
        ]);

        $post = new Post([
            'title' => 'Launch',
            'slug' => 'launch',
            'meta_json' => [
                'description' => 'Post description',
                'og_title' => 'Post social title',
            ],
        ]);

        $this->assertSame('About page description', $page->meta()['description']);
        $this->assertSame('About', $page->seoTitleFallback());
        $this->assertSame('Post description', $post->meta()['description']);
        $this->assertSame('Launch', $post->seoTitleFallback());
    }

    public function test_public_pages_and_posts_render_seo_from_the_shared_contract(): void
    {
        Page::create([
            'title' => 'About',
            'slug' => 'about',
            'content_json' => [
                'meta' => [
                    'description' => 'About page description',
                ],
            ],
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        Post::create([
            'title' => 'Launch',
            'slug' => 'launch',
            'excerpt' => 'Short launch summary',
            'content' => 'Launch body',
            'meta_json' => [
                'description' => 'Post description',
            ],
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $this->get(route('page.show', 'about'))
            ->assertOk()
            ->assertSee('<title>About', false)
            ->assertSee('content="About page description"', false)
            ->assertSee('href="'.route('page.show', 'about').'"', false);

        $this->get(route('blog.show', 'launch'))
            ->assertOk()
            ->assertSee('<title>Launch', false)
            ->assertSee('content="Post description"', false)
            ->assertSee('href="'.route('blog.show', 'launch').'"', false);
    }
}
