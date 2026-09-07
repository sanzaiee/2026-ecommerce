<?php

use Database\Seeders\CatalogSeeder;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

it('responds with valid xml sitemap', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
        ->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false);
});

it('includes static pages and dynamic models in sitemap', function () {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertSee('<loc>'.route('home').'</loc>', false)
        ->assertSee('<loc>'.route('shop').'</loc>', false)
        ->assertSee('<loc>'.route('product.show', 'premium-dried-mango-slices').'</loc>', false)
        ->assertSee('<loc>'.route('category.show', 'dried-fruits').'</loc>', false)
        ->assertSee('<loc>'.route('brand.show', 'mandira').'</loc>', false);
});

it('responds with plain text robots.txt declaring sitemap', function () {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('User-agent: *', false)
        ->assertSee('Sitemap: '.route('sitemap.index'), false);
});
