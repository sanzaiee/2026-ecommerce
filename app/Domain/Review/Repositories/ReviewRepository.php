<?php

namespace App\Domain\Review\Repositories;

use App\Domain\Review\DTOs\ReviewFilterData;
use App\Domain\Review\Models\Review;
use App\Enums\ReviewStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function create(array $attributes): Review
    {
        return Review::create($attributes);
    }

    public function findById(int $id): ?Review
    {
        return Review::with('product')->find($id);
    }

    public function paginateForAdmin(ReviewFilterData $filters): LengthAwarePaginator
    {
        $query = Review::with('product')->latest();

        if ($filters->status) {
            $query->where('status', $filters->status);
        }

        if ($filters->productId) {
            $query->where('product_id', $filters->productId);
        }

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    public function approve(Review $review): Review
    {
        $review->update(['status' => ReviewStatus::Approved]);

        return $review->fresh('product');
    }

    public function delete(Review $review): void
    {
        $review->delete();
    }

    public function count(): int
    {
        return Review::count();
    }

    public function countPending(): int
    {
        return Review::where('status', ReviewStatus::Pending)->count();
    }

}
