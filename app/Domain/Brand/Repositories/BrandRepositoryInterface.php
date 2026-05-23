<?php

namespace App\Domain\Brand\Repositories;

use App\Domain\Brand\DTOs\BrandFilterData;
use App\Domain\Brand\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BrandRepositoryInterface
{
    /**
     * @return Collection<int, Brand>
     */
    public function all(): Collection;

    public function paginateForAdmin(BrandFilterData $filters): LengthAwarePaginator;

    public function findById(int $id): ?Brand;

    public function findBySlug(string $slug): ?Brand;

    public function create(array $attributes): Brand;

    public function update(Brand $brand, array $attributes): Brand;

    public function delete(Brand $brand): void;

    public function count(): int;
}
