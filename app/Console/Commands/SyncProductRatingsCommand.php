<?php

namespace App\Console\Commands;

use App\Domain\Product\Models\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Services\CacheService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('products:sync-ratings')]
#[Description('Recalculate product rating_avg and review_count from approved reviews')]
class SyncProductRatingsCommand extends Command
{
    public function handle(ProductRepositoryInterface $products, CacheService $cache): int
    {
        $synced = 0;

        Product::query()->orderBy('id')->chunkById(100, function ($chunk) use ($products, &$synced): void {
            foreach ($chunk as $product) {
                $products->recalculateRating($product);
                $synced++;
            }
        });

        $cache->forgetAllCatalog();

        $this->info("Synced ratings for {$synced} product(s).");

        return self::SUCCESS;
    }
}
