<?php

namespace App\Domain\Customer\DTOs;

readonly class CustomerFilterData
{
    public function __construct(
        public ?string $search = null,
        public ?bool $isBanned = null,
        public ?bool $hasOrders = null,
        public ?string $orderBy = null,
        public string $orderDirection = 'desc',
        public int $perPage = 20,
        public int $page = 1,
    ) {}
}