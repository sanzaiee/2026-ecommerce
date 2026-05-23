<?php

namespace App\Domain\Contact\DTOs;

readonly class ContactMessageFilterData
{
    public function __construct(
        public ?string $search = null,
        public ?string $read = null,
        public int $perPage = 20,
        public int $page = 1,
    ) {}
}
