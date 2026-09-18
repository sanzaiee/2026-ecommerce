<?php

use App\Domain\Journey\Models\JourneyStage;
use App\Domain\Product\Models\Product;
use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\JourneyStageSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();
});

it('lists journey stages in admin filtered by product', function () {
    $this->seed(JourneyStageSeeder::class);

    $this->actingAs($this->admin)
        ->get(route('admin.journey-stages.index', ['product_id' => $this->product->id]))
        ->assertOk()
        ->assertSee('Journey Stages', false)
        ->assertSee($this->product->title, false)
        ->assertSee('Clay', false)
        ->assertSee('Finish', false);
});

it('shows a journey button on the products index', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.products.index'))
        ->assertOk()
        ->assertSee(route('admin.products.journey.edit', $this->product), false)
        ->assertSee('Journey', false);
});

it('opens a five-step journey editor for a product', function () {
    $this->seed(JourneyStageSeeder::class);

    $this->actingAs($this->admin)
        ->get(route('admin.products.journey.edit', $this->product))
        ->assertOk()
        ->assertSee($this->product->title, false)
        ->assertSee('Step 1', false)
        ->assertSee('Step 5', false)
        ->assertSee('Clay', false)
        ->assertSee('Finish', false);
});

it('saves all five journey steps for a product from one form', function () {
    Storage::fake('public');

    $payload = [
        'steps' => [
            [
                'title' => 'Clay',
                'text' => 'Fine river clay is prepared for this piece.',
                'image' => UploadedFile::fake()->image('clay.jpg', 640, 480),
            ],
            [
                'title' => 'Shape',
                'text' => 'The potter draws the form upward on the wheel.',
            ],
            [
                'title' => 'Dry',
                'text' => 'Vessels rest until moisture has left the walls.',
            ],
            [
                'title' => 'Fire',
                'text' => 'An open kiln firing hardens the clay.',
            ],
            [
                'title' => 'Finish',
                'text' => 'Fired pieces are polished and checked.',
            ],
        ],
    ];

    $this->actingAs($this->admin)
        ->put(route('admin.products.journey.update', $this->product), $payload)
        ->assertRedirect(route('admin.products.journey.edit', $this->product));

    $stages = JourneyStage::query()
        ->where('product_id', $this->product->id)
        ->orderBy('sort_order')
        ->get();

    expect($stages)->toHaveCount(5)
        ->and($stages->pluck('title')->all())->toBe(['Clay', 'Shape', 'Dry', 'Fire', 'Finish'])
        ->and($stages->first()->hasImage())->toBeTrue()
        ->and($stages->first()->text)->toContain('river clay');
});

it('requires all five journey step titles and descriptions', function () {
    $this->actingAs($this->admin)
        ->from(route('admin.products.journey.edit', $this->product))
        ->put(route('admin.products.journey.update', $this->product), [
            'steps' => [
                ['title' => 'Clay', 'text' => 'Ready clay.'],
                ['title' => '', 'text' => ''],
                ['title' => 'Dry', 'text' => 'Sun drying.'],
                ['title' => 'Fire', 'text' => 'Kiln fire.'],
                ['title' => 'Finish', 'text' => 'Final polish.'],
            ],
        ])
        ->assertRedirect(route('admin.products.journey.edit', $this->product))
        ->assertSessionHasErrors(['steps.1.title', 'steps.1.text']);
});

it('creates a journey stage for a specific product with an image', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)
        ->post(route('admin.journey-stages.store'), [
            'product_id' => $this->product->id,
            'title' => 'Polish',
            'text' => 'Each vessel is burnished by hand before it leaves the workshop.',
            'sort_order' => 6,
            'is_published' => '1',
            'image' => UploadedFile::fake()->image('polish.jpg', 640, 480),
        ])
        ->assertRedirect(route('admin.journey-stages.index', ['product_id' => $this->product->id]));

    $stage = JourneyStage::query()
        ->where('product_id', $this->product->id)
        ->where('title', 'Polish')
        ->first();

    expect($stage)->not->toBeNull()
        ->and($stage->text)->toContain('burnished')
        ->and($stage->hasImage())->toBeTrue();
});

it('shows only that product journey stages on the product page', function () {
    Storage::fake('public');

    $other = Product::query()->where('slug', '!=', $this->product->slug)->firstOrFail();

    $stage = JourneyStage::query()->create([
        'product_id' => $this->product->id,
        'title' => 'Clay',
        'text' => 'Fine river clay is prepared for this piece.',
        'sort_order' => 1,
        'is_published' => true,
    ]);
    $stage->addMedia(UploadedFile::fake()->image('clay.jpg', 640, 480))
        ->toMediaCollection('journey_stages');

    JourneyStage::query()->create([
        'product_id' => $this->product->id,
        'title' => 'Hidden Draft',
        'text' => 'This should not appear on the storefront.',
        'sort_order' => 2,
        'is_published' => false,
    ]);

    JourneyStage::query()->create([
        'product_id' => $other->id,
        'title' => 'Other Product Stage',
        'text' => 'Belongs to a different product.',
        'sort_order' => 1,
        'is_published' => true,
    ]);

    $imageUrl = $stage->fresh('media')->imageUrl('medium');

    $this->get(route('product.show', $this->product->slug))
        ->assertOk()
        ->assertSee('From Clay to Finished Pot', false)
        ->assertSee('Fine river clay is prepared for this piece.', false)
        ->assertSee($imageUrl, false)
        ->assertDontSee('Hidden Draft', false)
        ->assertDontSee('Other Product Stage', false);
});
