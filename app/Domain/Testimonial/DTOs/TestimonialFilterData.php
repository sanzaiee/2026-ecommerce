<?php

namespace App\Domain\Testimonial\DTOs;

readonly class TestimonialFilterData
{
    public function __construct(
        public ?string $search = null,
        public ?string $published = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public int $perPage = 10,
        public int $page = 1,
    ) {}
}
