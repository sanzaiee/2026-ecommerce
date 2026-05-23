<?php

namespace App\Domain\CMS\Repositories;

use App\Domain\CMS\Models\LandingPage;

interface LandingPageRepositoryInterface
{
    public function getSingleton(): LandingPage;

    public function update(LandingPage $landingPage, array $attributes): LandingPage;

    /**
     * @param  array<int, int>  $categoryIds  id => sort_order
     */
    public function syncCategories(LandingPage $landingPage, array $categoryIds): void;

    /**
     * @param  array<int, array{section: string, sort_order: int}>  $productPivots
     */
    public function syncProducts(LandingPage $landingPage, array $productPivots): void;
}
