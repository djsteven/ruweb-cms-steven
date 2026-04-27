<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Taxonomy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPublicTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_lists_only_published_posts(): void
    {
        Post::create([
            'title' => 'Published Post',
            'slug' => 'published-post',
            'excerpt' => 'Excerpt',
            'content' => 'Body',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        Post::create([
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'status' => 'draft',
        ]);

        $response = $this->get(route('blog.index'));

        $response->assertOk();
        $response->assertSee('Published Post');
        $response->assertDontSee('Draft Post');
    }

    public function test_blog_show_returns_404_for_unpublished_post(): void
    {
        Post::create([
            'title' => 'Future Post',
            'slug' => 'future-post',
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        $response = $this->get(route('blog.show', 'future-post'));

        $response->assertNotFound();
    }

    public function test_blog_index_can_filter_posts_by_category(): void
    {
        $news = Taxonomy::create([
            'name' => 'News',
            'slug' => 'news',
            'type' => 'category',
        ]);

        $updates = Taxonomy::create([
            'name' => 'Updates',
            'slug' => 'updates',
            'type' => 'category',
        ]);

        $newsPost = Post::create([
            'title' => 'News Post',
            'slug' => 'news-post',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);
        $newsPost->taxonomies()->attach($news);

        $updatesPost = Post::create([
            'title' => 'Updates Post',
            'slug' => 'updates-post',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);
        $updatesPost->taxonomies()->attach($updates);

        $response = $this->get(route('blog.index', ['category' => 'news']));

        $response->assertOk();
        $response->assertSee('News Post');
        $response->assertDontSee('Updates Post');
    }
}
