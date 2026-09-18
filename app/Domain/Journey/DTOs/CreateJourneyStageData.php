<?php

namespace App\Domain\Journey\DTOs;

use Illuminate\Http\UploadedFile;

readonly class CreateJourneyStageData
{
    public function __construct(
        public int $productId,
        public string $title,
        public string $text,
        public int $sortOrder = 0,
        public bool $isPublished = true,
        public ?UploadedFile $image = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'title' => $this->title,
            'text' => $this->text,
            'sort_order' => $this->sortOrder,
            'is_published' => $this->isPublished,
        ];
    }
}
