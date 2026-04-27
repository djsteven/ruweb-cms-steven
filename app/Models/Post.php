<?php

namespace App\Models;

use App\Contracts\Editorial\Mediable;
use App\Contracts\Editorial\Previewable;
use App\Contracts\Editorial\Publishable;
use App\Contracts\Editorial\Seoable;
use App\Traits\HasMedia;
use App\Traits\HasPublicationState;
use App\Traits\HasTaxonomies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model implements Mediable, Previewable, Publishable, Seoable
{
    use HasMedia, HasPublicationState, HasTaxonomies;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'meta_json',
        'status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'meta_json' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function meta(): array
    {
        return $this->meta_json ?? [];
    }

    public function url(): string
    {
        return route('blog.show', $this->slug);
    }

    public function seoTitleFallback(): ?string
    {
        return $this->title;
    }

    public function previewView(): string
    {
        return 'blog.show';
    }

    public function previewData(): array
    {
        return [
            'post' => $this,
            'page' => $this,
        ];
    }
}
