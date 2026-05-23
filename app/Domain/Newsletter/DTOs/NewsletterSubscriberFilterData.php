<?php

namespace App\Domain\Newsletter\DTOs;

readonly class NewsletterSubscriberFilterData
{
    public function __construct(
        public ?string $search = null,
        public string $status = 'active',
        public int $perPage = 20,
        public int $page = 1,
    ) {}
}
