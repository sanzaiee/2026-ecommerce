<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\CreateProductData;
use App\Domain\Product\DTOs\ProductFilterData;
use App\Domain\Product\DTOs\UpdateProductData;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Services\CacheService;
use App\Services\FileUploadService;
use App\Services\SEOService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
class ProductService
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private ProductRepositoryInterface $repository,
        private FileUploadService $uploads,
        private CacheService $cache,
        private SEOService $seo,
    ) {}

    public function paginate(ProductFilterData $filters): LengthAwarePaginator
    {
        $key = 'products.list.'.md5(serialize($filters));

        return $this->cache->remember($key, self::CACHE_TTL, fn () => $this->repository->paginate($filters));
    }

    public function paginateForAdmin(ProductFilterData $filters): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->cache->remember("products.{$slug}", self::CACHE_TTL, fn () => $this->repository->findBySlug($slug));
    }

    public function findById(int $id): ?Product
    {
        return $this->repository->findById($id);
    }

    /**
     * @return Collection<int, Product>
     */
    public function related(Product $product, int $limit = 4): Collection
    {
        return $this->repository->related($product, $limit);
    }

    public function create(CreateProductData $data): Product
    {
        $attributes = $this->seo->normalizeSeoFields($data->toArray());
        $attributes['slug'] = $attributes['slug'] ?? $this->uniqueSlug($data->title);

        $product = $this->repository->create($attributes);

        if ($data->images) {
            $this->uploads->syncCollection($product, 'products', $data->images);
        }

        $this->cache->forgetProduct($product->slug);

        return $product->fresh(['category', 'brand', 'media']);
    }

    public function update(UpdateProductData $data): Product
    {
        $product = $this->repository->findById($data->id);

        if (! $product) {
            throw new \RuntimeException('Product not found.');
        }

        $oldSlug = $product->slug;
        $attributes = $this->seo->normalizeSeoFields($data->toArray());

        if (! empty($attributes['slug']) && $attributes['slug'] !== $oldSlug) {
            $attributes['slug'] = $this->uniqueSlug($attributes['slug'], $product->id);
        }

        $product = $this->repository->update($product, $attributes);

        if ($data->images) {
            $this->uploads->syncCollection($product, 'products', $data->images);
        }

        $this->cache->forgetProduct($oldSlug);
        $this->cache->forgetProduct($product->slug);

        return $product->fresh(['category', 'brand', 'media']);
    }

    public function removeGalleryMedia(int $productId, int $mediaId): Product
    {
        $product = $this->repository->findById($productId);

        if (! $product) {
            throw new \RuntimeException('Product not found.');
        }

        $media = $product->getMedia('products')->firstWhere('id', $mediaId);

        if (! $media) {
            throw new \RuntimeException('Image not found.');
        }

        $this->uploads->deleteMedia($media);
        $this->cache->forgetProduct($product->slug);

        return $product->fresh(['category', 'brand', 'media']);
    }

    public function delete(int $id): void
    {
        $product = $this->repository->findById($id);

        if (! $product) {
            throw new \RuntimeException('Product not found.');
        }

        $slug = $product->slug;
        $this->repository->delete($product);
        $this->cache->forgetProduct($slug);
    }

    private function uniqueSlug(string $title, ?int $exceptId = null): string
    {
        $slug = $this->seo->slugify($title);
        $original = $slug;
        $i = 1;

        while (Product::where('slug', $slug)->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))->exists()) {
            $slug = $original.'-'.$i++;
        }

        return $slug;
    }
}
