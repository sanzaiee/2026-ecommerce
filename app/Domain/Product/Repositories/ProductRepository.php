<?php

namespace App\Domain\Product\Repositories;

use App\Domain\Brand\Models\Brand;
use App\Domain\Category\Models\Category;
use App\Domain\Product\DTOs\ProductFilterData;
use App\Domain\Product\Models\Product;
use App\Enums\StockStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    private const EAGER = ['category', 'brand', 'media'];

    public function paginate(ProductFilterData $filters): LengthAwarePaginator
    {
        $query = Product::query()->with(self::EAGER);

        match ($filters->visibility) {
            'trashed' => $query->onlyTrashed(),
            'all' => $query->withTrashed(),
            default => null,
        };

        if ($filters->categorySlug) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $filters->categorySlug));
        }

        if ($filters->categoryId) {
            $query->where('category_id', $filters->categoryId);
        }

        if ($filters->brandSlug) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $filters->brandSlug));
        }

        if ($filters->brandId) {
            $query->where('brand_id', $filters->brandId);
        }

        if ($filters->search) {
            $search = '%'.$filters->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        if ($filters->stockStatus) {
            $query->where('stock_status', $filters->stockStatus);
        }

        if ($filters->dateFrom) {
            $query->whereDate('created_at', '>=', $filters->dateFrom);
        }

        if ($filters->dateTo) {
            $query->whereDate('created_at', '<=', $filters->dateTo);
        }

        match ($filters->sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating' => $query->orderByDesc('rating_avg'),
            default => $query->orderByDesc('created_at'),
        };

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::with([...self::EAGER, 'approvedReviews'])
            ->where('slug', $slug)
            ->first();
    }

    public function findById(int $id): ?Product
    {
        return Product::with(self::EAGER)->find($id);
    }

    public function related(Product $product, int $limit = 4): Collection
    {
        return Product::with(self::EAGER)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit($limit)
            ->get();
    }

    public function create(array $attributes): Product
    {
        return Product::create($attributes);
    }

    public function update(Product $product, array $attributes): Product
    {
        $product->update($attributes);

        return $product->fresh(self::EAGER);
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function count(): int
    {
        return Product::count();
    }

    public function countOutOfStock(): int
    {
        return Product::where('stock_status', StockStatus::OutOfStock)->count();
    }

    public function updateRating(Product $product, float $avg, int $count): void
    {
        $product->update([
            'rating_avg' => $avg,
            'review_count' => $count,
        ]);
    }

    public function listWithReviewsForFilter(): Collection
    {
        return Product::query()
            ->whereHas('reviews')
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    public function listForSelect(): Collection
    {
        return Product::query()
            ->orderBy('title')
            ->get(['id', 'title']);
    }
}
