<?php

namespace App\Domain\Brand\Repositories;

use App\Domain\Brand\DTOs\BrandFilterData;
use App\Domain\Brand\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BrandRepository implements BrandRepositoryInterface
{
    public function all(): Collection
    {
        return Brand::with('media')->withCount('products')->orderBy('name')->get();
    }

    public function paginateForAdmin(BrandFilterData $filters): LengthAwarePaginator
    {
        $query = Brand::query()->with('media')->withCount('products')->orderBy('name');

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

    public function findById(int $id): ?Brand
    {
        return Brand::with('media')->find($id);
    }

    public function findBySlug(string $slug): ?Brand
    {
        return Brand::with('media')->where('slug', $slug)->first();
    }

    public function create(array $attributes): Brand
    {
        return Brand::create($attributes);
    }

    public function update(Brand $brand, array $attributes): Brand
    {
        $brand->update($attributes);

        return $brand->fresh('media');
    }

    public function delete(Brand $brand): void
    {
        $brand->delete();
    }

    public function count(): int
    {
        return Brand::count();
    }
}
