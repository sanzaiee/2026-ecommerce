<?php

namespace App\Domain\Product\Observers;

use App\Domain\Product\Models\Product;
use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Services\SEOService;

class ProductObserver
{
    public function __construct(private SEOService $seo) {}

    public function creating(Product $product): void
    {
        $this->applySeo($product);
    }

    public function updating(Product $product): void
    {
        $this->applySeo($product);
    }

    public function created(Product $product): void
    {
        ProductCreated::dispatch($product);
    }

    public function updated(Product $product): void
    {
        ProductUpdated::dispatch($product);
    }

    private function applySeo(Product $product): void
    {
        $normalized = $this->seo->normalizeSeoFields([
            'title' => $product->title,
            'slug' => $product->slug,
            'meta_title' => $product->meta_title,
            'meta_description' => $product->meta_description,
            'meta_keywords' => $product->meta_keywords,
        ]);

        $product->slug = $normalized['slug'] ?? $product->slug;
        $product->meta_title = $normalized['meta_title'] ?? $product->meta_title;
        $product->meta_keywords = $normalized['meta_keywords'] ?? $product->meta_keywords;
    }
}
