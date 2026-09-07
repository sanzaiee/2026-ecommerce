<?php

use App\Domain\CMS\Models\LandingPage;
use Database\Seeders\CatalogSeeder;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

it('renders pottery seo title and description on the home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<title>JheeKuma Clay Arts | Handmade Pottery from Thimi, Nepal</title>', false)
        ->assertSee('name="description" content="Shop handmade terracotta pots, gamala, and clay water filters', false);
});

it('renders pottery hero and landing section copy on the home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Handmade Newar pottery from Thimi — for home, garden and ritual.', false)
        ->assertSee('Terracotta planting pots, clay water vessels and everyday ware', false)
        ->assertSee('Shop Pottery', false)
        ->assertSee('Meet the Potters', false)
        ->assertSee('Pottery for garden and home', false)
        ->assertSee('Everyday clay for the kitchen and courtyard', false)
        ->assertSee('Most loved vessels from Thimi', false)
        ->assertSee('Bring home a piece of Thimi', false);
});

it('renders pottery footer chrome without food leftovers', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Handmade clay crafts from the potters of Thimi', false)
        ->assertSee('New firings, seasonal vessels and craft stories', false)
        ->assertSee('Handmade in Thimi', false)
        ->assertDontSee('Fried', false)
        ->assertDontSee('Frozen', false)
        ->assertDontSee('dried fruits and traditional pickles', false)
        ->assertDontSee('100% natural products', false);
});

it('falls back to pottery seo when cms meta fields are empty', function () {
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
        ->assertSee('<title>JheeKuma Clay Arts | Handmade Pottery from Thimi, Nepal</title>', false)
        ->assertSee('name="description" content="Shop handmade terracotta pots, gamala, and clay water filters', false)
        ->assertSee('Handmade Newar pottery from Thimi — for home, garden and ritual.', false);
});
