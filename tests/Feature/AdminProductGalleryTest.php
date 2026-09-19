<?php

use App\Domain\Product\Models\Product;
use App\Enums\StockStatus;
use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();
});

it('shows featured and additional photo fields on the product form', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.products.edit', $this->product))
        ->assertOk()
        ->assertSee('Featured image', false)
        ->assertSee('Additional photos', false)
        ->assertSee('name="featured_image"', false)
        ->assertSee('name="gallery_images[]"', false);
});

it('creates a product with a featured image and additional gallery photos', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)
        ->post(route('admin.products.store'), [
            'title' => 'Clay Water Pot',
            'price' => 1200,
            'stock_status' => StockStatus::InStock->value,
            'stock_quantity' => 8,
            'category_id' => $this->product->category_id,
            'brand_id' => $this->product->brand_id,
            'featured_image' => UploadedFile::fake()->image('featured.jpg', 800, 800),
            'gallery_images' => [
                UploadedFile::fake()->image('side.jpg', 800, 800),
                UploadedFile::fake()->image('detail.jpg', 800, 800),
            ],
        ])
        ->assertRedirect(route('admin.products.index'));

    $created = Product::query()->where('slug', 'clay-water-pot')->first();

    expect($created)->not->toBeNull()
        ->and($created->galleryMedia())->toHaveCount(3)
        ->and($created->additionalMedia())->toHaveCount(2);
});

it('appends additional photos without replacing the featured image', function () {
    Storage::fake('public');

    $this->product->addMedia(UploadedFile::fake()->image('existing-featured.jpg', 640, 640))
        ->toMediaCollection('products');
    $this->product->addMedia(UploadedFile::fake()->image('existing-extra.jpg', 640, 640))
        ->toMediaCollection('products');

    $featuredId = $this->product->fresh('media')->featuredMedia()?->id;

    $this->actingAs($this->admin)
        ->put(route('admin.products.update', $this->product), [
            'title' => $this->product->title,
            'slug' => $this->product->slug,
            'price' => $this->product->price,
            'stock_status' => $this->product->stock_status->value,
            'stock_quantity' => $this->product->stock_quantity,
            'category_id' => $this->product->category_id,
            'brand_id' => $this->product->brand_id,
            'gallery_images' => [
                UploadedFile::fake()->image('new-angle.jpg', 800, 800),
            ],
        ])
        ->assertRedirect(route('admin.products.index'));

    $updated = $this->product->fresh('media');

    expect($updated->galleryMedia())->toHaveCount(3)
        ->and($updated->featuredMedia()?->id)->toBe($featuredId)
        ->and($updated->additionalMedia())->toHaveCount(2);
});

it('replaces only the featured image when a new featured upload is provided', function () {
    Storage::fake('public');

    $this->product->addMedia(UploadedFile::fake()->image('old-featured.jpg', 640, 640))
        ->toMediaCollection('products');
    $this->product->addMedia(UploadedFile::fake()->image('keep-extra.jpg', 640, 640))
        ->toMediaCollection('products');

    $extraId = $this->product->fresh('media')->additionalMedia()->first()?->id;

    $this->actingAs($this->admin)
        ->put(route('admin.products.update', $this->product), [
            'title' => $this->product->title,
            'slug' => $this->product->slug,
            'price' => $this->product->price,
            'stock_status' => $this->product->stock_status->value,
            'stock_quantity' => $this->product->stock_quantity,
            'category_id' => $this->product->category_id,
            'brand_id' => $this->product->brand_id,
            'featured_image' => UploadedFile::fake()->image('new-featured.jpg', 800, 800),
        ])
        ->assertRedirect(route('admin.products.index'));

    $updated = $this->product->fresh('media');

    expect($updated->galleryMedia())->toHaveCount(2)
        ->and($updated->featuredMedia()?->file_name)->toBe('new-featured.jpg')
        ->and($updated->additionalMedia()->first()?->id)->toBe($extraId);
});

it('renders all product gallery images on the storefront detail page', function () {
    Storage::fake('public');

    $this->product->addMedia(UploadedFile::fake()->image('main.jpg', 800, 800))
        ->toMediaCollection('products');
    $this->product->addMedia(UploadedFile::fake()->image('angle.jpg', 800, 800))
        ->toMediaCollection('products');

    $urls = $this->product->fresh('media')->galleryUrls('large');

    $response = $this->get(route('product.show', $this->product->slug))
        ->assertOk()
        ->assertSee('product-gallery__thumbs', false);

    foreach ($urls as $url) {
        $response->assertSee($url, false);
    }
});
