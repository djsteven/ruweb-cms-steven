<?php

namespace App\Policies;

use App\Models\Taxonomy;
use App\Models\User;

class TaxonomyPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, config('cms.roles', []));
    }

    public function view(User $user, Taxonomy $taxonomy): bool
    {
        return in_array($user->role, config('cms.roles', []));
    }

    public function create(User $user): bool
    {
        return in_array($user->role, config('cms.roles', []));
    }

    public function update(User $user, Taxonomy $taxonomy): bool
    {
        return in_array($user->role, config('cms.roles', []));
    }

    public function delete(User $user, Taxonomy $taxonomy): bool
    {
        return $user->isAdmin();
    }
}
