<?php

namespace App\Domain\Brand\DTOs;

readonly class BrandFilterData
{
    public function __construct(
        public ?string $search = null,
        public ?string $visibility = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public int $perPage = 10,
        public int $page = 1,
    ) {}
}
