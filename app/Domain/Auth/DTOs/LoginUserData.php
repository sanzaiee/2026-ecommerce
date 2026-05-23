<?php

namespace App\Domain\Auth\DTOs;

use App\Enums\UserRole;

readonly class LoginUserData
{
    public function __construct(
        public string $email,
        public string $password,
        public ?UserRole $requiredRole = null,
    ) {}
}
