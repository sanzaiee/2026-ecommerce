<?php

namespace App\Domain\Category\Observers;

use App\Domain\Category\Models\Category;
use App\Events\CategoryUpdated;
use App\Services\SEOService;

class CategoryObserver
{
    public function __construct(private SEOService $seo) {}

    public function creating(Category $category): void
    {
        $this->applySeo($category);
    }

    public function updating(Category $category): void
    {
        $this->applySeo($category);
    }

    public function updated(Category $category): void
    {
        CategoryUpdated::dispatch($category);
    }

    private function applySeo(Category $category): void
    {
        $normalized = $this->seo->normalizeSeoFields([
            'name' => $category->name,
            'slug' => $category->slug,
            'meta_title' => $category->meta_title,
            'meta_description' => $category->meta_description,
            'meta_keywords' => $category->meta_keywords,
        ], 'name');

        $category->slug = $normalized['slug'] ?? $category->slug;
        $category->meta_title = $normalized['meta_title'] ?? $category->meta_title;
        $category->meta_keywords = $normalized['meta_keywords'] ?? $category->meta_keywords;
    }
}
