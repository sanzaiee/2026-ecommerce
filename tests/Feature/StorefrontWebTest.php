<?php

use App\Domain\Blog\Models\Blog;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\JourneyStageSeeder;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

it('renders home page', function () {
    $this->get(route('home'))->assertOk();
});

it('renders shop page', function () {
    $this->get(route('shop'))->assertOk();
});

it('renders product detail by slug route', function () {
    $this->get(route('product.show', 'premium-dried-mango-slices'))->assertOk();
});

it('renders the journey section on the product detail page', function () {
    $this->seed(JourneyStageSeeder::class);

    $this->get(route('product.show', 'premium-dried-mango-slices'))
        ->assertOk()
        ->assertSee('The Journey', false)
        ->assertSee('From Clay to Finished Pot', false)
        ->assertSee('Follow the complete lifecycle of Premium Dried Mango Slices', false)
        ->assertSee('Clay', false)
        ->assertSee('Shape', false)
        ->assertSee('Finish', false)
        ->assertSee('product-journey__placeholder', false);
});

it('renders category page by slug route', function () {
    $this->get(route('category.show', 'dried-fruits'))->assertOk();
});

it('redirects legacy product url', function () {
    $this->get('/products/premium-dried-mango-slices')
        ->assertRedirect('/product/premium-dried-mango-slices');
});

it('renders home page pottery sections', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Everyday Pottery', false)
        ->assertSee('Shop By Category', false)
        ->assertSee('More Than Clay', false)
        ->assertSee('The Potter&rsquo;s Process', false)
        ->assertSee('From Thimi, With Tradition', false);
});

it('hides stories section when no blogs exist', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Stories From the Tradition', false);
});

it('shows latest stories when blogs exist', function () {
    $posts = collect([
        Blog::factory()->create(['position' => 1, 'is_featured' => false]),
        Blog::factory()->create(['position' => 5, 'is_featured' => false]),
        Blog::factory()->create(['position' => 3, 'is_featured' => false]),
        Blog::factory()->create(['position' => 2, 'is_featured' => false]),
        Blog::factory()->create(['position' => 4, 'is_featured' => false]),
    ]);
    $top = $posts->firstWhere('position', 5);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Stories From the Tradition', false)
        ->assertSee($top->title)
        ->assertSee(route('blog.index'));
});
