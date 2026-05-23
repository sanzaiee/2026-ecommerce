<?php

namespace App\Listeners;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Review\Models\Review;
use App\Enums\ReviewStatus;
use App\Events\ReviewApproved;

class UpdateProductRating
{
    public function __construct(private ProductRepositoryInterface $products) {}

    public function handle(ReviewApproved $event): void
    {
        $product = $event->review->product;

        if (! $product) {
            return;
        }

        $stats = Review::query()
            ->where('product_id', $product->id)
            ->where('status', ReviewStatus::Approved)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        $this->products->updateRating(
            $product,
            round((float) ($stats->avg_rating ?? 0), 2),
            (int) ($stats->total ?? 0)
        );
    }
}
