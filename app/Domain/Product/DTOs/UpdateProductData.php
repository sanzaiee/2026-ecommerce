<?php

namespace App\Domain\Product\DTOs;

use App\Enums\StockStatus;
use Illuminate\Http\UploadedFile;

readonly class UpdateProductData
{
    /**
     * @param  array<int, UploadedFile>|null  $images
     * @param  array<int, string>|null  $bullets
     * @param  array<int, array{label: string, value: string}>|null  $additionalInfo
     */
    public function __construct(
        public int $id,
        public string $title,
        public ?string $slug,
        public ?string $description,
        public ?string $shortDescription,
        public float $price,
        public ?float $oldPrice,
        public StockStatus $stockStatus,
        public int $stockQuantity = 0,
        public ?int $categoryId,
        public ?int $brandId,
        public ?string $metaTitle,
        public ?string $metaDescription,
        public ?string $metaKeywords,
        public bool $isFeatured = false,
        public int $sortOrder = 0,
        public ?array $bullets = null,
        public ?array $additionalInfo = null,
        public ?array $images = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->shortDescription,
            'price' => $this->price,
            'old_price' => $this->oldPrice,
            'stock_status' => $this->stockStatus,
            'stock_quantity' => $this->stockQuantity,
            'category_id' => $this->categoryId,
            'brand_id' => $this->brandId,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'meta_keywords' => $this->metaKeywords,
            'is_featured' => $this->isFeatured,
            'sort_order' => $this->sortOrder,
            'bullets' => $this->bullets,
            'additional_info' => $this->additionalInfo,
        ];
    }
}
