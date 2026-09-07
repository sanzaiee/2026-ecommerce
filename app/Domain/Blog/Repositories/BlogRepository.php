<?php

namespace App\Domain\Blog\Repositories;

use App\Domain\Blog\DTOs\BlogFilterData;
use App\Domain\Blog\DTOs\BlogStorefrontFilterData;
use App\Domain\Blog\Models\Blog;
use App\Domain\Blog\Models\BlogCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BlogRepository implements BlogRepositoryInterface
{
    public function paginateForAdmin(BlogFilterData $filters): LengthAwarePaginator
    {
        $query = Blog::query()
            ->with(['media', 'category'])
            ->ordered();

        if ($filters->search) {
            $search = '%'.$filters->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('content', 'like', $search);
            });
        }

        if ($filters->featured === 'featured') {
            $query->featured();
        } elseif ($filters->featured === 'standard') {
            $query->where('is_featured', false);
        }

        if ($filters->dateFrom) {
            $query->whereDate('created_at', '>=', $filters->dateFrom);
        }

        if ($filters->dateTo) {
            $query->whereDate('created_at', '<=', $filters->dateTo);
        }

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    public function paginateForStorefront(BlogStorefrontFilterData $filters): LengthAwarePaginator
    {
        $query = Blog::query()
            ->with(['media', 'category'])
            ->select(['id', 'title', 'slug', 'blog_category_id', 'excerpt', 'content', 'position', 'is_featured', 'created_at']);

        if ($filters->search) {
            $search = '%'.$filters->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('excerpt', 'like', $search)
                    ->orWhere('content', 'like', $search);
            });
        }

        if ($filters->category) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $filters->category));
        }

        if ($filters->featured === 'featured') {
            $query->featured();
        } elseif ($filters->featured === 'standard') {
            $query->where('is_featured', false);
        }

        return $query
            ->ordered()
            ->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    public function categoriesForStorefront(): Collection
    {
        return BlogCategory::query()
            ->ordered()
            ->withCount('blogs')
            ->get();
    }

    public function latestForStorefront(int $limit = 3): Collection
    {
        return Blog::query()
            ->with(['media', 'category'])
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public function findById(int $id): ?Blog
    {
        return Blog::query()->with('media')->find($id);
    }

    public function findBySlug(string $slug): ?Blog
    {
        return Blog::query()->with('media')->where('slug', $slug)->first();
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        return Blog::query()
            ->where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }

    public function create(array $attributes): Blog
    {
        return Blog::create($attributes);
    }

    public function update(Blog $blog, array $attributes): Blog
    {
        $blog->update($attributes);

        return $blog->fresh('media');
    }

    public function delete(Blog $blog): void
    {
        $blog->delete();
    }
}
