<?php

namespace App\Domain\Blog\Repositories;

use App\Domain\Blog\DTOs\BlogFilterData;
use App\Domain\Blog\DTOs\BlogStorefrontFilterData;
use App\Domain\Blog\Models\Blog;
use App\Domain\Blog\Models\BlogCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BlogRepositoryInterface
{
    public function paginateForAdmin(BlogFilterData $filters): LengthAwarePaginator;

    public function paginateForStorefront(BlogStorefrontFilterData $filters): LengthAwarePaginator;

    /**
     * @return Collection<int, BlogCategory>
     */
    public function categoriesForStorefront(): Collection;

    public function findById(int $id): ?Blog;

    public function findBySlug(string $slug): ?Blog;

    public function slugExists(string $slug, ?int $exceptId = null): bool;

    public function create(array $attributes): Blog;

    public function update(Blog $blog, array $attributes): Blog;

    public function delete(Blog $blog): void;
}
