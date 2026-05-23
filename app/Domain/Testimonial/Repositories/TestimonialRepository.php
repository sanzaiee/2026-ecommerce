<?php

namespace App\Domain\Testimonial\Repositories;

use App\Domain\Testimonial\DTOs\TestimonialFilterData;
use App\Domain\Testimonial\Models\Testimonial;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TestimonialRepository implements TestimonialRepositoryInterface
{
    public function paginateForAdmin(TestimonialFilterData $filters): LengthAwarePaginator
    {
        $query = Testimonial::query()
            ->with('product:id,title,slug')
            ->orderByDesc('sort_order')
            ->orderByDesc('created_at');

        if ($filters->search) {
            $search = '%'.$filters->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('text', 'like', $search);
            });
        }

        if ($filters->published === 'published') {
            $query->where('is_published', true);
        } elseif ($filters->published === 'draft') {
            $query->where('is_published', false);
        }

        if ($filters->dateFrom) {
            $query->whereDate('created_at', '>=', $filters->dateFrom);
        }

        if ($filters->dateTo) {
            $query->whereDate('created_at', '<=', $filters->dateTo);
        }

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    public function findById(int $id): ?Testimonial
    {
        return Testimonial::with('product:id,title,slug')->find($id);
    }

    public function publishedForHome(int $limit): Collection
    {
        return Testimonial::query()
            ->with('product:id,title,slug')
            ->where('is_published', true)
            ->where('text', '!=', '')
            ->orderByDesc('sort_order')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function create(array $attributes): Testimonial
    {
        return Testimonial::create($attributes);
    }

    public function update(Testimonial $testimonial, array $attributes): Testimonial
    {
        $testimonial->update($attributes);

        return $testimonial->fresh('product');
    }

    public function delete(Testimonial $testimonial): void
    {
        $testimonial->delete();
    }
}
