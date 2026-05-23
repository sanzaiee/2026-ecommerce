<?php

namespace App\Support\ViewData;

use App\Domain\CMS\Models\LandingPage;
use App\Domain\Product\Models\Product;

class LandingPageMapper
{
    public function __construct(private StorefrontProductMapper $products) {}

    /**
     * @return array<string, mixed>
     */
    public function toHomeView(LandingPage $page, array $everyday, array $topSelling): array
    {
        $heroImage = $page->getFirstMediaUrl('cms', 'large')
            ?: 'https://images.unsplash.com/photo-1608797178974-15b35a8edeaa?w=800&q=80';

        return [
            'hero' => [
                'title' => $page->hero_title,
                'subtitle' => $page->hero_subtitle,
                'image' => $heroImage,
                'imageAlt' => $page->hero_title,
            ],
            'categories' => $page->featuredCategories->map(fn ($c) => [
                'title' => $c->name,
                'image' => $c->imageUrl('medium')
                    ?: 'https://images.unsplash.com/photo-1599599810769-0a29d5affa8e?w=700&q=80',
                'href' => route('category.show', $c->slug),
            ])->values()->all(),
            'everydayProducts' => array_map(
                fn (Product $p) => $this->products->toLandingCard($p),
                $everyday
            ),
            'topSellingProducts' => array_map(
                fn (Product $p) => $this->products->toLandingCard($p),
                $topSelling
            ),
        ];
    }
}
