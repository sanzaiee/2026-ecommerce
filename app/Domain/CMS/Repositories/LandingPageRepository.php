<?php

namespace App\Domain\CMS\Repositories;

use App\Domain\CMS\Models\LandingPage;

class LandingPageRepository implements LandingPageRepositoryInterface
{
    public function getSingleton(): LandingPage
    {
        $page = LandingPage::with([
            'media',
            'featuredCategories.media',
            'featuredProducts' => fn ($q) => $q->with(['media', 'category']),
        ])->find(1);

        if ($page) {
            return $page;
        }

        return LandingPage::create([
            'hero_title' => 'Handmade Newar pottery from Thimi — for home, garden and ritual.',
            'hero_subtitle' => 'Terracotta planting pots, clay water vessels and everyday ware — shaped on the wheel in Madhyapur.',
            'meta_title' => 'JheeKuma Clay Arts | Handmade Pottery from Thimi, Nepal',
            'meta_description' => 'Shop handmade terracotta pots, gamala, and clay water filters from Thimi potters. Fired by hand in Madhyapur — shipped across Nepal.',
            'meta_keywords' => 'Thimi pottery, Nepal clay pots, Newar pottery, clay water filter Nepal, terracotta plant pots Kathmandu, Madhyapur pottery',
        ]);
    }

    public function update(LandingPage $landingPage, array $attributes): LandingPage
    {
        $landingPage->update($attributes);

        return $landingPage->fresh([
            'media',
            'featuredCategories.media',
            'featuredProducts.media',
        ]);
    }

    public function syncCategories(LandingPage $landingPage, array $categoryIds): void
    {
        $sync = [];
        foreach ($categoryIds as $order => $id) {
            $categoryId = (int) $id;
            if ($categoryId <= 0) {
                continue;
            }
            $sync[$categoryId] = ['sort_order' => $order];
        }
        $landingPage->featuredCategories()->sync($sync);
    }

    public function syncProducts(LandingPage $landingPage, array $productPivots): void
    {
        $landingPage->featuredProducts()->sync($productPivots);
    }
}
