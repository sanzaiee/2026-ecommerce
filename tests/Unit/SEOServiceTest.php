<?php

use App\Services\SEOService;

it('generates slug from title', function () {
    $seo = new SEOService;

    expect($seo->slugify('Premium Dried Mango'))->toBe('premium-dried-mango');
});

it('fills meta title fallback from title', function () {
    $seo = new SEOService;

    $result = $seo->normalizeSeoFields([
        'title' => 'Mango Slices',
        'meta_title' => null,
        'slug' => null,
    ]);

    expect($result['meta_title'])->toBe('Mango Slices')
        ->and($result['slug'])->toBe('mango-slices');
});
