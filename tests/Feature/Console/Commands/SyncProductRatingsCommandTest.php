<?php

use App\Domain\Product\Models\Product;
use App\Domain\Review\Models\Review;
use App\Enums\ReviewStatus;
use Database\Seeders\CatalogSeeder;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

it('recalculates stale product ratings from approved reviews', function () {
    $product = Product::query()->where('slug', 'organic-dried-banana-chips')->firstOrFail();

    $product->update([
        'rating_avg' => 4.5,
        'review_count' => 18,
    ]);

    Review::query()->create([
        'product_id' => $product->id,
        'name' => 'Prod Reviewer',
        'rating' => 4,
        'comment' => 'Good.',
        'status' => ReviewStatus::Approved,
    ]);

    Review::query()->create([
        'product_id' => $product->id,
        'name' => 'Pending Prod Reviewer',
        'rating' => 1,
        'comment' => 'Pending.',
        'status' => ReviewStatus::Pending,
    ]);

    $this->artisan('products:sync-ratings')
        ->expectsOutputToContain('Synced ratings for')
        ->assertSuccessful();

    $product->refresh();

    expect((float) $product->rating_avg)->toBe(4.0)
        ->and($product->review_count)->toBe(1);
});

it('zeros ratings when a product has no approved reviews', function () {
    $product = Product::query()->where('slug', 'organic-dried-banana-chips')->firstOrFail();

    $product->update([
        'rating_avg' => 5,
        'review_count' => 31,
    ]);

    $this->artisan('products:sync-ratings')->assertSuccessful();

    $product->refresh();

    expect((float) $product->rating_avg)->toBe(0.0)
        ->and($product->review_count)->toBe(0);
});
