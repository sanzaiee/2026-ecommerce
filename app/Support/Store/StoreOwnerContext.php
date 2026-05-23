<?php

namespace App\Support\Store;

use App\Models\User;

readonly class StoreOwnerContext
{
    public function __construct(
        public ?User $user,
        public string $guestToken,
    ) {}

    public function isAuthenticated(): bool
    {
        return $this->user !== null;
    }
}
