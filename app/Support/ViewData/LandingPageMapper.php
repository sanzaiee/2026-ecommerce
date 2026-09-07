<?php

namespace App\Support\ViewData;

use App\Domain\CMS\Models\LandingPage;
use App\Domain\Product\Models\Product;
use App\Support\ViewData\DTOs\Seo;

class LandingPageMapper
{
    public function __construct(private StorefrontProductMapper $products) {}

    /**
     * SEO payload for the home page: CMS meta fields first, pottery copy as fallback.
     */
    public function toSeo(LandingPage $page): Seo
    {
        return new Seo(
            title: $page->meta_title
                ?: ($page->hero_title ?: 'JheeKuma Clay Arts | Handmade Pottery from Thimi, Nepal'),
            description: $page->meta_description
                ?: ($page->hero_subtitle ?: 'Shop handmade terracotta pots, gamala, and clay water filters from Thimi potters. Fired by hand in Madhyapur — shipped across Nepal.'),
            keywords: $page->meta_keywords
                ? array_values(array_filter(array_map('trim', explode(',', $page->meta_keywords))))
                : ['Thimi pottery', 'Nepal clay pots', 'Newar pottery', 'clay water filter Nepal', 'terracotta plant pots Kathmandu', 'Madhyapur pottery'],
            canonicalPath: '/',
            image: $this->heroImage($page),
        );
    }

    /**
     * @param  array<int, Product>  $everyday
     * @param  array<int, Product>  $topSelling
     * @return array<string, mixed>
     */
    public function toHomeView(LandingPage $page, array $everyday, array $topSelling): array
    {
        return [
            'hero' => [
                'title' => $page->hero_title ?: 'Handmade Newar pottery from Thimi — for home, garden and ritual.',
                'subtitle' => $page->hero_subtitle
                    ?: 'Terracotta planting pots, clay water vessels and everyday ware — shaped on the wheel in Madhyapur.',
                'image' => $this->heroImage($page),
                'imageAlt' => $page->hero_title ?: 'Handmade Newar pottery from Thimi, Nepal',
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
