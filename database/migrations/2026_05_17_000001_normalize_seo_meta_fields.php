<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalizePages();
        $this->normalizePosts();
    }

    public function down(): void
    {
        // Irreversible data normalization. Old og_* keys are intentionally removed.
    }

    private function normalizePages(): void
    {
        DB::table('pages')->select(['id', 'content_json'])->orderBy('id')->chunk(100, function ($pages): void {
            foreach ($pages as $page) {
                $content = json_decode($page->content_json ?? '[]', true);
                if (! is_array($content)) {
                    $content = [];
                }

                $meta = is_array($content['meta'] ?? null) ? $content['meta'] : [];
                $normalized = $this->normalizeMeta($meta);

                if ($normalized === $meta) {
                    continue;
                }

                $content['meta'] = $normalized;

                DB::table('pages')->where('id', $page->id)->update([
                    'content_json' => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
            }
        });
    }

    private function normalizePosts(): void
    {
        DB::table('posts')->select(['id', 'meta_json'])->orderBy('id')->chunk(100, function ($posts): void {
            foreach ($posts as $post) {
                $meta = json_decode($post->meta_json ?? '[]', true);
                if (! is_array($meta)) {
                    $meta = [];
                }

                $normalized = $this->normalizeMeta($meta);
                if ($normalized === $meta) {
                    continue;
                }

                DB::table('posts')->where('id', $post->id)->update([
                    'meta_json' => json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
            }
        });
    }

    private function normalizeMeta(array $meta): array
    {
        if (! filled($meta['title'] ?? null) && filled($meta['og_title'] ?? null)) {
            $meta['title'] = $meta['og_title'];
        }

        if (! filled($meta['description'] ?? null) && filled($meta['og_description'] ?? null)) {
            $meta['description'] = $meta['og_description'];
        }

        unset($meta['og_title'], $meta['og_description']);

        return $meta;
    }
};
