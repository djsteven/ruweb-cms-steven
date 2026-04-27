<?php

namespace App\Models;

use App\Contracts\Editorial\Mediable;
use App\Contracts\Editorial\Previewable;
use App\Contracts\Editorial\Publishable;
use App\Contracts\Editorial\Seoable;
use App\Traits\HasMedia;
use App\Traits\HasPublicationState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\View;

class Page extends Model implements Mediable, Previewable, Publishable, Seoable
{
    use HasMedia, HasPublicationState;

    protected $fillable = [
        'title',
        'slug',
        'template_key',
        'content_json',
        'status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'content_json' => 'array',
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
        return $this->content_json['meta'] ?? [];
    }

    public function sections(): array
    {
        return $this->content_json['sections'] ?? [];
    }

    public function resolveTemplate(): string
    {
        $template = 'templates.'.$this->template_key;

        if (View::exists($template)) {
            return $template;
        }

        return 'templates.default';
    }

    public function url(): string
    {
        return '/'.ltrim($this->slug, '/');
    }

    public function seoTitleFallback(): ?string
    {
        return $this->title;
    }

    public function previewView(): string
    {
        return $this->resolveTemplate();
    }

    public function previewData(): array
    {
        return ['page' => $this];
    }
}
