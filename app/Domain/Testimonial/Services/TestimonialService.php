<?php

namespace App\Domain\Testimonial\Services;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Testimonial\DTOs\CreateTestimonialData;
use App\Domain\Testimonial\DTOs\TestimonialFilterData;
use App\Domain\Testimonial\DTOs\UpdateTestimonialData;
use App\Domain\Testimonial\Models\Testimonial;
use App\Domain\Testimonial\Repositories\TestimonialRepositoryInterface;
use App\Services\CacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TestimonialService
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private TestimonialRepositoryInterface $repository,
        private ProductRepositoryInterface $products,
        private CacheService $cache,
    ) {}

    public function paginateForAdmin(TestimonialFilterData $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($filters);
    }

    public function findById(int $id): ?Testimonial
    {
        return $this->repository->findById($id);
    }

    /**
     * @return Collection<int, \App\Domain\Product\Models\Product>
     */
    public function productOptionsForForm(): Collection
    {
        return $this->products->listForSelect();
    }

    public function create(CreateTestimonialData $data): Testimonial
    {
        $testimonial = $this->repository->create($data->toArray());
        $this->cache->forgetTestimonials();

        return $testimonial->fresh('product');
    }

    public function update(UpdateTestimonialData $data): Testimonial
    {
        $testimonial = $this->repository->findById($data->id);

        if (! $testimonial) {
            throw new \RuntimeException('Testimonial not found.');
        }

        $testimonial = $this->repository->update($testimonial, $data->toArray());
        $this->cache->forgetTestimonials();

        return $testimonial;
    }

    public function delete(int $id): void
    {
        $testimonial = $this->repository->findById($id);

        if (! $testimonial) {
            throw new \RuntimeException('Testimonial not found.');
        }

        $this->repository->delete($testimonial);
        $this->cache->forgetTestimonials();
    }

    /**
     * @return array<int, array{name: string, rating: int, text: string, productName: string|null, productUrl: string|null}>
     */
    public function forHome(int $limit = 6): array
    {
        return $this->cache->remember('testimonials.home', self::CACHE_TTL, function () use ($limit) {
            return $this->repository->publishedForHome($limit)
                ->map(fn (Testimonial $testimonial) => [
                    'name' => $testimonial->name,
                    'rating' => $testimonial->rating,
                    'text' => $testimonial->text,
                    'productName' => $testimonial->product?->title,
                    'productUrl' => $testimonial->product
                        ? route('product.show', $testimonial->product->slug)
                        : null,
                ])
                ->all();
        });
    }
}
