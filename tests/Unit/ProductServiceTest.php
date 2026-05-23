<?php

use App\Domain\Product\DTOs\ProductFilterData;
use App\Domain\Product\Services\ProductService;
use Database\Seeders\CatalogSeeder;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

it('finds product by slug via service', function () {
    $service = app(ProductService::class);

    $product = $service->findBySlug('premium-dried-mango-slices');

    expect($product)->not->toBeNull()
        ->and($product->title)->toBe('Premium Dried Mango Slices');
});

it('paginates products with category filter', function () {
    $service = app(ProductService::class);

    $result = $service->paginate(new ProductFilterData(categorySlug: 'pickles', perPage: 10));

    expect($result->total())->toBe(2);
});
