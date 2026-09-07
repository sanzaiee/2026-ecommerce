<?php

namespace App\Listeners;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
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

        $this->products->recalculateRating($product);
    }
}
