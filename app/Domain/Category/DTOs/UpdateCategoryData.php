<?php

namespace App\Domain\Category\DTOs;

use Illuminate\Http\UploadedFile;

readonly class UpdateCategoryData
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $slug,
        public ?string $description,
        public ?string $metaTitle,
        public ?string $metaDescription,
        public ?string $metaKeywords,
        public int $sortOrder = 0,
        public ?UploadedFile $image = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'meta_keywords' => $this->metaKeywords,
            'sort_order' => $this->sortOrder,
        ];
    }
}
