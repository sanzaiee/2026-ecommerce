<?php

namespace App\Domain\Category\Repositories;

use App\Domain\Category\DTOs\CategoryFilterData;
use App\Domain\Category\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all(): Collection
    {
        return Category::with('media')->orderBy('sort_order')->get();
    }

    public function paginateForAdmin(CategoryFilterData $filters): LengthAwarePaginator
    {
        $query = Category::query()->with('media')->withCount('products')->orderBy('sort_order');

        match ($filters->visibility) {
            'trashed' => $query->onlyTrashed(),
            'all' => $query->withTrashed(),
            default => null,
        };

        if ($filters->search) {
            $search = '%'.$filters->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('slug', 'like', $search);
            });
        }

        if ($filters->dateFrom) {
            $query->whereDate('created_at', '>=', $filters->dateFrom);
        }

        if ($filters->dateTo) {
            $query->whereDate('created_at', '<=', $filters->dateTo);
        }

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::with(['media', 'products' => fn ($q) => $q->with(['media', 'category', 'brand'])])
            ->where('slug', $slug)
            ->first();
    }

    public function findById(int $id): ?Category
    {
        return Category::with('media')->find($id);
    }

    public function create(array $attributes): Category
    {
        return Category::create($attributes);
    }

    public function update(Category $category, array $attributes): Category
    {
        $category->update($attributes);

        return $category->fresh('media');
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }

    public function count(): int
    {
        return Category::count();
    }
}
