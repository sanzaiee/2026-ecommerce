<?php

namespace App\Domain\Blog\DTOs;

readonly class BlogFilterData
{
    public function __construct(
        public ?string $search = null,
        public ?string $featured = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public int $perPage = 10,
        public int $page = 1,
    ) {}
}
