<?php

namespace App\Domain\Product\Repositories;

use App\Domain\Product\DTOs\ProductFilterData;
use App\Domain\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function paginate(ProductFilterData $filters): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Product;

    public function findById(int $id): ?Product;

    /**
     * @return Collection<int, Product>
     */
    public function related(Product $product, int $limit = 4): Collection;

    public function create(array $attributes): Product;

    public function update(Product $product, array $attributes): Product;

    public function delete(Product $product): void;

    public function count(): int;

    public function countOutOfStock(): int;

    public function updateRating(Product $product, float $avg, int $count): void;

    public function recalculateRating(Product $product): void;

    /**
     * @return Collection<int, Product>
     */
    public function listWithReviewsForFilter(): Collection;

    /**
     * @return Collection<int, Product>
     */
    public function listForSelect(): Collection;
}
