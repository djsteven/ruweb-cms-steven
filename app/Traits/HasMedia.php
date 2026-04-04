<?php

namespace App\Traits;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasMedia
{
    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable')
            ->withPivot('collection', 'order')
            ->withTimestamps()
            ->orderBy('mediables.order');
    }

    public function mediaByCollection(string $collection): MorphToMany
    {
        return $this->media()->wherePivot('collection', $collection);
    }

    public function attachMedia(int $mediaId, ?string $collection = null, int $order = 0): void
    {
        $this->media()->attach($mediaId, [
            'collection' => $collection,
            'order' => $order,
        ]);
    }

    public function featuredImage(): ?Media
    {
        return $this->mediaByCollection('featured_image')->first();
    }
}
