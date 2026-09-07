<?php

namespace App\Domain\Review\Services;

use App\Domain\Product\Models\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Review\DTOs\CreateReviewData;
use App\Domain\Review\DTOs\ReviewFilterData;
use App\Domain\Review\Models\Review;
use App\Domain\Review\Repositories\ReviewRepositoryInterface;
use App\Events\ReviewApproved;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ReviewService
{
    public function __construct(
        private ReviewRepositoryInterface $repository,
        private ProductRepositoryInterface $products,
    ) {}

    public function create(CreateReviewData $data): Review
    {
        $product = $this->products->findById($data->productId);

        if (! $product) {
            throw new \RuntimeException('Product not found.');
        }

        return $this->repository->create($data->toArray());
    }

    public function paginateForAdmin(ReviewFilterData $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($filters);
    }

    /**
     * @return Collection<int, Product>
     */
    public function filterProductOptions(): Collection
    {
        return $this->products->listWithReviewsForFilter();
    }

    public function approve(int $id): Review
    {
        $review = $this->repository->findById($id);

        if (! $review) {
            throw new \RuntimeException('Review not found.');
        }

        $review = $this->repository->approve($review);
        ReviewApproved::dispatch($review);

        return $review;
    }

    public function delete(int $id): void
    {
        $review = $this->repository->findById($id);

        if (! $review) {
            throw new \RuntimeException('Review not found.');
        }

        $product = $review->product;
        $this->repository->delete($review);

        if ($product) {
            $this->products->recalculateRating($product);
        }
    }
}
