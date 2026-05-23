<?php

namespace App\Domain\Brand\Services;

use App\Domain\Brand\DTOs\BrandFilterData;
use App\Domain\Brand\DTOs\CreateBrandData;
use App\Domain\Brand\DTOs\UpdateBrandData;
use App\Domain\Brand\Models\Brand;
use App\Domain\Brand\Repositories\BrandRepositoryInterface;
use App\Services\FileUploadService;
use App\Services\SEOService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BrandService
{
    public function __construct(
        private BrandRepositoryInterface $repository,
        private FileUploadService $uploads,
        private SEOService $seo,
    ) {}

    /**
     * @return Collection<int, Brand>
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function paginateForAdmin(BrandFilterData $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($filters);
    }

    public function findById(int $id): ?Brand
    {
        return $this->repository->findById($id);
    }

    public function findBySlug(string $slug): ?Brand
    {
        return $this->repository->findBySlug($slug);
    }

    public function create(CreateBrandData $data): Brand
    {
        $attributes = $this->seo->normalizeSeoFields($data->toArray(), 'name');
        $attributes['slug'] = $attributes['slug'] ?? $this->uniqueSlug($data->name);

        $brand = $this->repository->create($attributes);
        $this->uploads->addSingle($brand, 'brands', $data->image);

        return $brand->fresh('media');
    }

    public function update(UpdateBrandData $data): Brand
    {
        $brand = $this->repository->findById($data->id);

        if (! $brand) {
            throw new \RuntimeException('Brand not found.');
        }

        $attributes = $this->seo->normalizeSeoFields($data->toArray(), 'name');

        if (! empty($attributes['slug'])) {
            $attributes['slug'] = $this->uniqueSlug($attributes['slug'], $brand->id);
        }

        $brand = $this->repository->update($brand, $attributes);
        $this->uploads->addSingle($brand, 'brands', $data->image);

        return $brand;
    }

    public function removeImage(int $id): Brand
    {
        $brand = $this->repository->findById($id);

        if (! $brand) {
            throw new \RuntimeException('Brand not found.');
        }

        $this->uploads->clearCollection($brand, 'brands');

        return $brand->fresh('media');
    }

    public function delete(int $id): void
    {
        $brand = $this->repository->findById($id);

        if (! $brand) {
            throw new \RuntimeException('Brand not found.');
        }

        $this->repository->delete($brand);
    }

    private function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $slug = $this->seo->slugify($name);
        $original = $slug;
        $i = 1;

        while (Brand::where('slug', $slug)->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))->exists()) {
            $slug = $original.'-'.$i++;
        }

        return $slug;
    }
}
