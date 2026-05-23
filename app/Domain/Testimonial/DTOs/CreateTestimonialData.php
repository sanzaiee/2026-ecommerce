<?php

namespace App\Domain\Testimonial\DTOs;

readonly class CreateTestimonialData
{
    public function __construct(
        public string $name,
        public int $rating,
        public string $text,
        public ?int $productId,
        public bool $isPublished,
        public int $sortOrder,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'rating' => $this->rating,
            'text' => $this->text,
            'product_id' => $this->productId,
            'is_published' => $this->isPublished,
            'sort_order' => $this->sortOrder,
        ];
    }
}
