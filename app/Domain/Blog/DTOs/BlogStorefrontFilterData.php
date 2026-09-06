<?php

namespace App\Domain\Blog\DTOs;

readonly class BlogStorefrontFilterData
{
    public function __construct(
        public ?string $search = null,
        public ?string $category = null,
        public ?string $featured = null,
        public int $perPage = 12,
        public int $page = 1,
    ) {}
}
