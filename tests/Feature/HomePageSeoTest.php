<?php

use App\Domain\CMS\Models\LandingPage;
use Database\Seeders\CatalogSeeder;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

it('renders cms meta title on home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<title>Traditional Clay Pottery from Thimi, Nepal — Newar Handmade Pots</title>', false);
});

it('renders meta description and keywords on home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('name="description" content="Handmade Newar pottery from Thimi, Nepal', false)
        ->assertSee('name="keywords" content="Thimi pottery, Nepal clay pots, Newar pottery', false);
});

it('renders canonical open graph and twitter meta on home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.config('app.url').'/"', false)
        ->assertSee('property="og:title" content="Traditional Clay Pottery from Thimi, Nepal — Newar Handmade Pots"', false)
        ->assertSee('property="og:type" content="website"', false)
        ->assertSee('name="twitter:card" content="summary_large_image"', false);
});

it('renders organization and website json-ld on home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('application/ld+json', false)
        ->assertSee('"@type":"Organization"', false)
        ->assertSee('"@type":"WebSite"', false)
        ->assertSee('SearchAction', false);
});

it('falls back to pottery copy when all cms meta fields are empty', function () {
    LandingPage::query()->update([
        'meta_title' => null,
        'meta_description' => null,
        'meta_keywords' => null,
        'hero_title' => null,
        'hero_subtitle' => null,
    ]);
    Cache::forget('landing.page');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<title>Traditional Clay Pottery from Thimi, Nepal</title>', false)
        ->assertSee('name="description" content="Handmade Newar pottery', false)
        ->assertSee('name="keywords" content="Thimi pottery, Nepal clay pots, Newar pottery', false);
});

it('uses hero title and subtitle as meta fallback when meta fields empty', function () {
    LandingPage::query()->update([
        'meta_title' => null,
        'meta_description' => null,
    ]);
    Cache::forget('landing.page');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<title>Where Clay Becomes Culture</title>', false)
        ->assertSee('name="description" content="Discover traditional clay pottery', false);
});
