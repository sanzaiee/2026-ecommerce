<?php

namespace App\Domain\CMS\Services;

use App\Domain\CMS\DTOs\UpdateLandingPageData;
use App\Domain\CMS\Models\LandingPage;
use App\Domain\CMS\Repositories\LandingPageRepositoryInterface;
use App\Domain\Product\Models\Product;
use App\Enums\LandingProductSection;
use App\Services\CacheService;
use App\Services\FileUploadService;
use App\Services\SEOService;

class CMSService
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private LandingPageRepositoryInterface $repository,
        private FileUploadService $uploads,
        private CacheService $cache,
        private SEOService $seo,
    ) {}

    public function getPublicPayload(): LandingPage
    {
        return $this->cache->remember('landing.page', self::CACHE_TTL, fn () => $this->repository->getSingleton());
    }

    public function update(UpdateLandingPageData $data): LandingPage
    {
        $page = $this->repository->getSingleton();

        if ($data->toArray() !== []) {
            $page = $this->repository->update(
                $page,
                $this->seo->normalizeSeoFields($data->toArray())
            );
        }

        $this->uploads->addSingle($page, 'cms', $data->heroImage);

        if ($data->featuredCategoryIds !== null) {
            $this->repository->syncCategories($page, $data->featuredCategoryIds);
        }

        if ($data->everydayProductIds !== null || $data->topSellingProductIds !== null) {
            $existing = $page->featuredProducts()
                ->get()
                ->mapWithKeys(fn ($p) => [
                    $p->id => [
                        'section' => $p->pivot->section,
                        'sort_order' => $p->pivot->sort_order,
                    ],
                ])
                ->all();

            $pivots = $existing;

            if ($data->everydayProductIds !== null) {
                $pivots = $this->withoutProductSection(
                    $pivots,
                    LandingProductSection::Everyday,
                );
            }

            if ($data->topSellingProductIds !== null) {
                $pivots = $this->withoutProductSection(
                    $pivots,
                    LandingProductSection::TopSelling,
                );
            }

            // array_merge renumbers numeric keys (product IDs); array_replace preserves them.
            $pivots = array_replace($pivots, $data->productPivots());
            $this->repository->syncProducts($page, $pivots);
        }

        $this->cache->forgetLanding();

        return $this->repository->getSingleton();
    }

    /**
     * @return array<int, Product>
     */
    public function everydayProducts(LandingPage $page): array
    {
        return $page->productsForSection(LandingProductSection::Everyday)
            ->with(['media', 'category'])
            ->get()
            ->all();
    }

    /**
     * @return array<int, Product>
     */
    public function topSellingProducts(LandingPage $page): array
    {
        return $page->productsForSection(LandingProductSection::TopSelling)
            ->with(['media', 'category'])
            ->get()
            ->all();
    }

    /**
     * @param  array<int, array{section: string, sort_order: int}>  $pivots
     * @return array<int, array{section: string, sort_order: int}>
     */
    private function withoutProductSection(array $pivots, LandingProductSection $section): array
    {
        return array_filter(
            $pivots,
            fn (array $pivot) => $pivot['section'] !== $section->value,
        );
    }
}
