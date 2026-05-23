<?php

namespace App\Policies;

use App\Domain\Testimonial\Models\Testimonial;
use App\Models\User;

class TestimonialPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Testimonial $testimonial): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Testimonial $testimonial): bool
    {
        return $user->isAdmin();
    }
}
