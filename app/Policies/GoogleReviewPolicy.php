<?php

namespace App\Policies;

use App\Models\GoogleReview;
use App\Models\User;

class GoogleReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, config('cms.roles', []));
    }

    public function view(User $user, GoogleReview $googleReview): bool
    {
        return in_array($user->role, config('cms.roles', []));
    }

    public function create(User $user): bool
    {
        return in_array($user->role, config('cms.roles', []));
    }

    public function update(User $user, GoogleReview $googleReview): bool
    {
        return in_array($user->role, config('cms.roles', []));
    }

    public function delete(User $user, GoogleReview $googleReview): bool
    {
        return $user->isAdmin();
    }
}
