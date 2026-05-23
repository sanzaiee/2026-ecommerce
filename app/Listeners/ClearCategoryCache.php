<?php

namespace App\Listeners;

use App\Events\CategoryUpdated;
use App\Services\CacheService;

class ClearCategoryCache
{
    public function __construct(private CacheService $cache) {}

    public function handle(CategoryUpdated $event): void
    {
        $this->cache->forgetCategory($event->category->slug);
    }
}
