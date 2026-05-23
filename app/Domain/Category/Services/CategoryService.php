<?php

namespace App\Domain\Category\Services;

use App\Domain\Category\DTOs\CategoryFilterData;
use App\Domain\Category\DTOs\CreateCategoryData;
use App\Domain\Category\DTOs\UpdateCategoryData;
use App\Domain\Category\Models\Category;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Services\CacheService;
use App\Services\FileUploadService;
use App\Services\SEOService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CategoryService
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private CategoryRepositoryInterface $repository,
        private FileUploadService $uploads,
        private CacheService $cache,
        private SEOService $seo,
    ) {}

    /**
     * @return Collection<int, Category>
     */
    public function all(): Collection
    {
        return $this->cache->remember('categories.all', self::CACHE_TTL, fn () => $this->repository->all());
    }

    public function paginateForAdmin(CategoryFilterData $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($filters);
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->cache->remember("categories.{$slug}", self::CACHE_TTL, fn () => $this->repository->findBySlug($slug));
    }

    public function findById(int $id): ?Category
    {
        return $this->repository->findById($id);
    }

    public function create(CreateCategoryData $data): Category
    {
        $attributes = $this->seo->normalizeSeoFields($data->toArray(), 'name');
        $attributes['slug'] = $attributes['slug'] ?? $this->uniqueSlug($data->name);

        $category = $this->repository->create($attributes);
        $this->uploads->addSingle($category, 'categories', $data->image);
        $this->cache->forgetCategory();

        return $category->fresh('media');
    }

    public function update(UpdateCategoryData $data): Category
    {
        $category = $this->repository->findById($data->id);

        if (! $category) {
            throw new \RuntimeException('Category not found.');
        }

        $oldSlug = $category->slug;
        $attributes = $this->seo->normalizeSeoFields($data->toArray(), 'name');

        if (! empty($attributes['slug']) && $attributes['slug'] !== $oldSlug) {
            $attributes['slug'] = $this->uniqueSlug($attributes['slug'], $category->id);
        }

        $category = $this->repository->update($category, $attributes);
        $this->uploads->addSingle($category, 'categories', $data->image);
        $this->cache->forgetCategory($oldSlug);
        $this->cache->forgetCategory($category->slug);

        return $category->fresh('media');
    }

    public function removeImage(int $id): Category
    {
        $category = $this->repository->findById($id);

        if (! $category) {
            throw new \RuntimeException('Category not found.');
        }

        $this->uploads->clearCollection($category, 'categories');
        $this->cache->forgetCategory($category->slug);

        return $category->fresh('media');
    }

    public function delete(int $id): void
    {
        $category = $this->repository->findById($id);

        if (! $category) {
            throw new \RuntimeException('Category not found.');
        }

        $slug = $category->slug;
        $this->repository->delete($category);
        $this->cache->forgetCategory($slug);
    }

    private function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $slug = $this->seo->slugify($name);
        $original = $slug;
        $i = 1;

        while (Category::where('slug', $slug)->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))->exists()) {
            $slug = $original.'-'.$i++;
        }

        return $slug;
    }
}
