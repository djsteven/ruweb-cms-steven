<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\View;

class Page extends Model
{
    use HasMedia;

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

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
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
        $template = 'templates.' . $this->template_key;

        if (View::exists($template)) {
            return $template;
        }

        return 'templates.default';
    }

    public function url(): string
    {
        return '/' . ltrim($this->slug, '/');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at && $this->published_at->lte(now());
    }
}
