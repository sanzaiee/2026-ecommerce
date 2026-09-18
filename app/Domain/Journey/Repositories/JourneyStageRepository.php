<?php

namespace App\Domain\Journey\Repositories;

use App\Domain\Journey\DTOs\JourneyStageFilterData;
use App\Domain\Journey\Models\JourneyStage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class JourneyStageRepository implements JourneyStageRepositoryInterface
{
    public function paginateForAdmin(JourneyStageFilterData $filters): LengthAwarePaginator
    {
        $query = JourneyStage::query()
            ->with(['media', 'product'])
            ->orderBy('product_id')
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($filters->productId) {
            $query->where('product_id', $filters->productId);
        }

        if ($filters->search) {
            $search = '%'.$filters->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('text', 'like', $search)
                    ->orWhereHas('product', fn ($product) => $product->where('title', 'like', $search));
            });
        }

        match ($filters->published) {
            'published' => $query->where('is_published', true),
            'draft' => $query->where('is_published', false),
            default => null,
        };

        if ($filters->dateFrom) {
            $query->whereDate('created_at', '>=', $filters->dateFrom);
        }

        if ($filters->dateTo) {
            $query->whereDate('created_at', '<=', $filters->dateTo);
        }

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    public function findById(int $id): ?JourneyStage
    {
        return JourneyStage::with(['media', 'product'])->find($id);
    }

    public function publishedForProduct(int $productId): Collection
    {
        return JourneyStage::query()
            ->with('media')
            ->where('product_id', $productId)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function forProduct(int $productId): Collection
    {
        return JourneyStage::query()
            ->with('media')
            ->where('product_id', $productId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function create(array $attributes): JourneyStage
    {
        return JourneyStage::create($attributes);
    }

    public function update(JourneyStage $stage, array $attributes): JourneyStage
    {
        $stage->update($attributes);

        return $stage->fresh(['media', 'product']);
    }

    public function delete(JourneyStage $stage): void
    {
        $stage->delete();
    }
}
