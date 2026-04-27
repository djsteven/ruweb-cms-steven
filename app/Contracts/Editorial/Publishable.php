<?php

namespace App\Contracts\Editorial;

use Illuminate\Database\Eloquent\Builder;

interface Publishable
{
    public function scopePublished(Builder $query): Builder;

    public function isPublished(): bool;
}
