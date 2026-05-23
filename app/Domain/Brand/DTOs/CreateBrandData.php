<?php

namespace App\Domain\Brand\DTOs;

use Illuminate\Http\UploadedFile;

readonly class CreateBrandData
{
    public function __construct(
        public string $name,
        public ?string $slug,
        public ?string $description,
        public ?string $metaTitle,
        public ?string $metaDescription,
        public ?string $metaKeywords,
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
        ];
    }
}
