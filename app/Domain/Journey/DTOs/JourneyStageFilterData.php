<?php

namespace App\Domain\Journey\DTOs;

readonly class JourneyStageFilterData
{
    public function __construct(
        public ?string $search = null,
        public ?int $productId = null,
        public ?string $published = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public int $perPage = 10,
        public int $page = 1,
    ) {}
}
