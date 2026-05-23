<?php

namespace App\Domain\Category\Repositories;

use App\Domain\Category\DTOs\CategoryFilterData;
use App\Domain\Category\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    /**
     * @return Collection<int, Category>
     */
    public function all(): Collection;

    public function paginateForAdmin(CategoryFilterData $filters): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Category;

    public function findById(int $id): ?Category;

    public function create(array $attributes): Category;

    public function update(Category $category, array $attributes): Category;

    public function delete(Category $category): void;

    public function count(): int;
}
