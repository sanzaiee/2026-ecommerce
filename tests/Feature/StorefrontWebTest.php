<?php

use Database\Seeders\CatalogSeeder;

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

it('renders category page by slug route', function () {
    $this->get(route('category.show', 'dried-fruits'))->assertOk();
});

it('redirects legacy product url', function () {
    $this->get('/products/premium-dried-mango-slices')
        ->assertRedirect('/product/premium-dried-mango-slices');
});
