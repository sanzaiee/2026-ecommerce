<?php

use App\Domain\Product\Models\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Review\Models\Review;
use App\Enums\ReviewStatus;
use Database\Seeders\CatalogSeeder;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

it('syncs seeded product ratings from approved reviews', function () {
    $mango = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();
    $trailMix = Product::query()->where('slug', 'himalayan-trail-mix')->firstOrFail();
    $unreviewed = Product::query()->where('slug', 'organic-dried-banana-chips')->firstOrFail();

    expect((float) $mango->rating_avg)->toBe(5.0)
        ->and($mango->review_count)->toBe(1)
        ->and((float) $trailMix->rating_avg)->toBe(4.0)
        ->and($trailMix->review_count)->toBe(1)
        ->and((float) $unreviewed->rating_avg)->toBe(0.0)
        ->and($unreviewed->review_count)->toBe(0);
});

it('renders database product ratings on the home page', function () {
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();

    $product->update([
        'rating_avg' => 4.5,
        'review_count' => 3,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('data-rating="4.5"', false)
        ->assertSee('aria-label="4.5 out of 5 stars"', false)
        ->assertSee('(3)', false);
});

it('hides product card ratings when a product has no reviews', function () {
    Product::query()->update([
        'rating_avg' => 0,
        'review_count' => 0,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('class="product-rating"', false);
});

it('recalculates product rating from approved reviews', function () {
    $product = Product::query()->where('slug', 'organic-dried-banana-chips')->firstOrFail();

    Review::query()->create([
        'product_id' => $product->id,
        'name' => 'Test Reviewer',
        'rating' => 4,
        'comment' => 'Solid snack.',
        'status' => ReviewStatus::Approved,
    ]);

    Review::query()->create([
        'product_id' => $product->id,
        'name' => 'Another Reviewer',
        'rating' => 5,
        'comment' => 'Loved it.',
        'status' => ReviewStatus::Approved,
    ]);

    Review::query()->create([
        'product_id' => $product->id,
        'name' => 'Pending Reviewer',
        'rating' => 1,
        'comment' => 'Still waiting.',
        'status' => ReviewStatus::Pending,
    ]);

    app(ProductRepositoryInterface::class)->recalculateRating($product->fresh());

    $product->refresh();

    expect((float) $product->rating_avg)->toBe(4.5)
        ->and($product->review_count)->toBe(2);
});
