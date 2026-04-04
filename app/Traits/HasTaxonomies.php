<?php

namespace App\Traits;

use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

trait HasTaxonomies
{
    public function taxonomies(): MorphToMany
    {
        return $this->morphToMany(Taxonomy::class, 'taxable')
            ->withTimestamps();
    }

    public function taxonomiesByType(string $type): MorphToMany
    {
        return $this->taxonomies()->where('type', $type);
    }

    public function categories(): MorphToMany
    {
        return $this->taxonomiesByType('category');
    }

    /**
     * Sync taxonomy terms of a given type without affecting other types.
     */
    public function syncTaxonomies(array $ids, string $type): void
    {
        $currentIds = $this->taxonomiesByType($type)->pluck('taxonomies.id');
        $this->taxonomies()->detach($currentIds);

        if (! empty($ids)) {
            $this->taxonomies()->attach($ids);
        }
    }
}
