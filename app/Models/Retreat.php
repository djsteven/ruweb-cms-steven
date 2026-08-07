<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Retreat extends Model
{
    use HasMedia;

    protected $fillable = [
        'title', 'slug', 'starts_at', 'ends_at', 'organizer', 'excerpt', 'content',
        'status', 'published_at', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return ['starts_at' => 'date', 'ends_at' => 'date', 'published_at' => 'datetime'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('ends_at', '>=', today())->orderBy('starts_at');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
