<?php

namespace App\Domain\Review\DTOs;

use App\Enums\ReviewStatus;

readonly class ReviewFilterData
{
    public function __construct(
        public ?ReviewStatus $status = null,
        public bool $hasStatusFilter = false,
        public ?int $productId = null,
        public int $perPage = 20,
        public int $page = 1,
    ) {}

    public function isAll(): bool
    {
        return $this->hasStatusFilter && $this->status === null;
    }
}
