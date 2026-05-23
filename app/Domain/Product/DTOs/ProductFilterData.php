<?php

namespace App\Domain\Product\DTOs;

readonly class ProductFilterData
{
    public function __construct(
        public ?string $categorySlug = null,
        public ?string $brandSlug = null,
        public ?int $categoryId = null,
        public ?int $brandId = null,
        public ?string $search = null,
        public ?string $stockStatus = null,
        public ?string $visibility = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public string $sort = 'newest',
        public int $perPage = 12,
        public int $page = 1,
    ) {}
}
