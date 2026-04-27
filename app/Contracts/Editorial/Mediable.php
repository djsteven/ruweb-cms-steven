<?php

namespace App\Contracts\Editorial;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface Mediable
{
    public function media(): MorphToMany;

    public function mediaByCollection(string $collection): MorphToMany;

    public function attachMedia(int $mediaId, ?string $collection = null, int $order = 0): void;

    public function featuredImage(): ?Media;
}
