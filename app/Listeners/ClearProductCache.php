<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Events\ReviewApproved;
use App\Services\CacheService;

class ClearProductCache
{
    public function __construct(private CacheService $cache) {}

    public function handle(ProductCreated|ProductUpdated|ReviewApproved $event): void
    {
        $slug = match (true) {
            $event instanceof ReviewApproved => $event->review->product?->slug,
            default => $event->product->slug,
        };

        $this->cache->forgetProduct($slug);
    }
}
