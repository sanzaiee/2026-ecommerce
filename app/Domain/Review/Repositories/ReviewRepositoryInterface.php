<?php

namespace App\Domain\Review\Repositories;

use App\Domain\Review\DTOs\ReviewFilterData;
use App\Domain\Review\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ReviewRepositoryInterface
{
    public function create(array $attributes): Review;

    public function findById(int $id): ?Review;

    public function paginateForAdmin(ReviewFilterData $filters): LengthAwarePaginator;

    public function approve(Review $review): Review;

    public function delete(Review $review): void;

    public function count(): int;

    public function countPending(): int;

    /**
     * @return Collection<int, Review>
     */
}
