<?php

namespace App\Domain\Review\DTOs;

readonly class CreateReviewData
{
    public function __construct(
        public int $productId,
        public string $name,
        public int $rating,
        public ?string $comment,
        public ?int $userId = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'user_id' => $this->userId,
            'name' => $this->name,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'status' => 'pending',
        ];
    }
}
