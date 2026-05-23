<?php

namespace App\Domain\Testimonial\Repositories;

use App\Domain\Testimonial\DTOs\TestimonialFilterData;
use App\Domain\Testimonial\Models\Testimonial;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TestimonialRepositoryInterface
{
    public function paginateForAdmin(TestimonialFilterData $filters): LengthAwarePaginator;

    public function findById(int $id): ?Testimonial;

    /**
     * @return Collection<int, Testimonial>
     */
    public function publishedForHome(int $limit): Collection;

    public function create(array $attributes): Testimonial;

    public function update(Testimonial $testimonial, array $attributes): Testimonial;

    public function delete(Testimonial $testimonial): void;
}
