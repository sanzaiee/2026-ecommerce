<?php

namespace App\Domain\Blog\DTOs;

use Illuminate\Http\UploadedFile;

readonly class CreateBlogData
{
    public function __construct(
        public string $title,
        public string $content,
        public ?string $excerpt,
        public ?int $blogCategoryId,
        public int $position,
        public bool $isFeatured,
        public ?UploadedFile $image = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'blog_category_id' => $this->blogCategoryId,
            'position' => $this->position,
            'is_featured' => $this->isFeatured,
        ];
    }
}
