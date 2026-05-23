<?php

namespace App\Domain\CMS\DTOs;

use App\Enums\LandingProductSection;
use Illuminate\Http\UploadedFile;

readonly class UpdateLandingPageData
{
    /**
     * @param  array<int, int>|null  $featuredCategoryIds
     * @param  array<int, int>|null  $everydayProductIds
     * @param  array<int, int>|null  $topSellingProductIds
     */
    public function __construct(
        public ?string $heroTitle,
        public ?string $heroSubtitle,
        public ?string $metaTitle,
        public ?string $metaDescription,
        public ?string $metaKeywords,
        public ?UploadedFile $heroImage = null,
        public ?array $featuredCategoryIds = null,
        public ?array $everydayProductIds = null,
        public ?array $topSellingProductIds = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'hero_title' => $this->heroTitle,
            'hero_subtitle' => $this->heroSubtitle,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'meta_keywords' => $this->metaKeywords,
        ], fn ($v) => $v !== null);
    }

    /**
     * @return array<string, array<int, int>>
     */
    public function productPivots(): array
    {
        $pivots = [];

        if ($this->everydayProductIds !== null) {
            foreach ($this->everydayProductIds as $order => $id) {
                $productId = (int) $id;
                if ($productId <= 0) {
                    continue;
                }
                $pivots[$productId] = [
                    'section' => LandingProductSection::Everyday->value,
                    'sort_order' => $order,
                ];
            }
        }

        if ($this->topSellingProductIds !== null) {
            foreach ($this->topSellingProductIds as $order => $id) {
                $productId = (int) $id;
                if ($productId <= 0) {
                    continue;
                }
                $pivots[$productId] = [
                    'section' => LandingProductSection::TopSelling->value,
                    'sort_order' => $order,
                ];
            }
        }

        return $pivots;
    }
}
