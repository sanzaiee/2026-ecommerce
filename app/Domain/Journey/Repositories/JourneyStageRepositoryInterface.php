<?php

namespace App\Domain\Journey\Repositories;

use App\Domain\Journey\DTOs\JourneyStageFilterData;
use App\Domain\Journey\Models\JourneyStage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface JourneyStageRepositoryInterface
{
    public function paginateForAdmin(JourneyStageFilterData $filters): LengthAwarePaginator;

    public function findById(int $id): ?JourneyStage;

    /**
     * @return Collection<int, JourneyStage>
     */
    public function publishedForProduct(int $productId): Collection;

    /**
     * @return Collection<int, JourneyStage>
     */
    public function forProduct(int $productId): Collection;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): JourneyStage;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(JourneyStage $stage, array $attributes): JourneyStage;

    public function delete(JourneyStage $stage): void;
}
