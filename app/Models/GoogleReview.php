<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GoogleReview extends Model
{
    protected $fillable = [
        'place_id',
        'author_name',
        'author_url',
        'profile_photo_url',
        'rating',
        'text',
        'relative_time_description',
        'review_time',
        'is_visible',
        'is_featured',
        'sort_order',
        'imported_at',
    ];

    protected function casts(): array
    {
        return [
            'rating'      => 'integer',
            'is_visible'  => 'boolean',
            'is_featured' => 'boolean',
            'sort_order'  => 'integer',
            'review_time' => 'datetime',
            'imported_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeByRating(Builder $query, int $min): Builder
    {
        return $query->where('rating', '>=', $min);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Devuelve un array con la representación de estrellas.
     * ['full' => 4, 'half' => 1, 'empty' => 0] para rating 4.5, por ejemplo.
     * Como Google usa enteros (1-5) simplemente devolvemos full/empty.
     */
    public function starsArray(): array
    {
        $full  = max(0, min(5, (int) $this->rating));
        $empty = 5 - $full;

        return [
            'full'  => $full,
            'half'  => 0,
            'empty' => $empty,
        ];
    }

    /**
     * URL del avatar: si Google provee una foto la usamos; si no, una inicial.
     */
    public function avatarUrl(): string
    {
        return $this->profile_photo_url
            ?? 'https://ui-avatars.com/api/?name='.urlencode($this->author_name).'&background=f0f0f0&color=555&size=64';
    }

    /**
     * Calcula el promedio de estrellas para un Place ID dado.
     */
    public static function averageRatingForPlace(string $placeId): float
    {
        return (float) static::where('place_id', $placeId)->avg('rating');
    }

    /**
     * Cuenta las reseñas visibles de un Place ID.
     */
    public static function visibleCountForPlace(string $placeId): int
    {
        return static::where('place_id', $placeId)->visible()->count();
    }

    /**
     * Cuenta todas las reseñas guardadas de un Place ID.
     */
    public static function totalCountForPlace(string $placeId): int
    {
        return static::where('place_id', $placeId)->count();
    }
}
