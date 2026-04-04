<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, config('cms.roles', []));
    }

    public function view(User $user, Post $post): bool
    {
        return in_array($user->role, config('cms.roles', []));
    }

    public function create(User $user): bool
    {
        return in_array($user->role, config('cms.roles', []));
    }

    public function update(User $user, Post $post): bool
    {
        return in_array($user->role, config('cms.roles', []));
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }
}
