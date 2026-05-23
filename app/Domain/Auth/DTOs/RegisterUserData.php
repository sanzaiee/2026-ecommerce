<?php

namespace App\Domain\Auth\DTOs;

use App\Enums\UserRole;

readonly class RegisterUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public UserRole $role = UserRole::Customer,
    ) {}
}
