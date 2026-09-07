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
            'hero_title' => 'Where Clay Becomes Culture',
            'hero_subtitle' => 'Discover traditional clay pottery shaped by generations of Newar craftsmanship in Thimi, Nepal.',
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
