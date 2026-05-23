<?php

namespace App\Policies;

use App\Domain\Review\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function approve(User $user, Review $review): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->isAdmin();
    }
}
