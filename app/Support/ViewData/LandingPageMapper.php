<?php

namespace App\Support\ViewData;

use App\Domain\CMS\Models\LandingPage;
use App\Domain\Product\Models\Product;

class LandingPageMapper
{
    public function __construct(private StorefrontProductMapper $products) {}

    /**
     * @param  array<int, Product>  $everyday
     * @param  array<int, Product>  $topSelling
     * @return array<string, mixed>
     */
    public function toHomeView(LandingPage $page, array $everyday, array $topSelling): array
    {
        return [
            'hero' => [
                'title' => $page->hero_title ?: 'Where Clay Becomes Culture',
                'subtitle' => $page->hero_subtitle
                    ?: 'Discover traditional clay pottery shaped by generations of Newar craftsmanship in Thimi, Nepal.',
                'image' => $this->heroImage($page),
                'imageAlt' => $page->hero_title ?: 'Traditional Newar clay pottery from Thimi, Nepal',
            ],
            'categories' => $page->featuredCategories
                ->take(5)
                ->map(fn ($c) => [
                    'title' => $c->name,
                    'description' => $c->description,
                    'image' => $c->imageUrl('medium') ?: get_placeholder_image(),
                    'href' => route('category.show', $c->slug),
                ])
                ->values()
                ->all(),
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

    /**
     * CMS-uploaded hero image, or a neutral pottery placeholder.
     */
    private function heroImage(LandingPage $page): string
    {
        return $page->getFirstMediaUrl('cms', 'large')
            ?: get_placeholder_image();
    }
}
