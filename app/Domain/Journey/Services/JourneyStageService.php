<?php

namespace App\Domain\Journey\Services;

use App\Domain\Journey\DTOs\CreateJourneyStageData;
use App\Domain\Journey\DTOs\JourneyStageFilterData;
use App\Domain\Journey\DTOs\SyncProductJourneyData;
use App\Domain\Journey\DTOs\UpdateJourneyStageData;
use App\Domain\Journey\Models\JourneyStage;
use App\Domain\Journey\Repositories\JourneyStageRepositoryInterface;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Services\CacheService;
use App\Services\FileUploadService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class JourneyStageService
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private JourneyStageRepositoryInterface $repository,
        private ProductRepositoryInterface $products,
        private FileUploadService $uploads,
        private CacheService $cache,
    ) {}

    public function paginateForAdmin(JourneyStageFilterData $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($filters);
    }

    public function findById(int $id): ?JourneyStage
    {
        return $this->repository->findById($id);
    }

    /**
     * @return Collection<int, Product>
     */
    public function productOptionsForForm(): Collection
    {
        return $this->products->listForSelect();
    }

    public function create(CreateJourneyStageData $data): JourneyStage
    {
        $stage = $this->repository->create($data->toArray());
        $this->uploads->addSingle($stage, 'journey_stages', $data->image);
        $this->cache->forgetJourneyStages($data->productId);

        return $stage->fresh(['media', 'product']);
    }

    public function update(UpdateJourneyStageData $data): JourneyStage
    {
        $stage = $this->repository->findById($data->id);

        if (! $stage) {
            throw new \RuntimeException('Journey stage not found.');
        }

        $previousProductId = (int) $stage->product_id;
        $stage = $this->repository->update($stage, $data->toArray());
        $this->uploads->addSingle($stage, 'journey_stages', $data->image);
        $this->cache->forgetJourneyStages($previousProductId);
        $this->cache->forgetJourneyStages($data->productId);

        return $stage;
    }

    public function removeImage(int $id): JourneyStage
    {
        $stage = $this->repository->findById($id);

        if (! $stage) {
            throw new \RuntimeException('Journey stage not found.');
        }

        $this->uploads->clearCollection($stage, 'journey_stages');
        $this->cache->forgetJourneyStages((int) $stage->product_id);

        return $stage->fresh(['media', 'product']);
    }

    public function delete(int $id): void
    {
        $stage = $this->repository->findById($id);

        if (! $stage) {
            throw new \RuntimeException('Journey stage not found.');
        }

        $productId = (int) $stage->product_id;
        $this->repository->delete($stage);
        $this->cache->forgetJourneyStages($productId);
    }

    /**
     * Replace a product's journey with exactly five ordered steps.
     *
     * @return Collection<int, JourneyStage>
     */
    public function syncForProduct(SyncProductJourneyData $data): Collection
    {
        return DB::transaction(function () use ($data) {
            $existing = $this->repository->forProduct($data->productId)->values();
            $keptIds = [];

            foreach ($data->steps as $index => $step) {
                $sortOrder = $index + 1;
                $stage = $existing->get($index);

                $attributes = [
                    'product_id' => $data->productId,
                    'title' => $step['title'],
                    'text' => $step['text'],
                    'sort_order' => $sortOrder,
                    'is_published' => true,
                ];

                if ($stage) {
                    $stage = $this->repository->update($stage, $attributes);
                } else {
                    $stage = $this->repository->create($attributes);
                }

                $this->uploads->addSingle($stage, 'journey_stages', $step['image']);
                $keptIds[] = $stage->id;
            }

            $existing
                ->reject(fn (JourneyStage $stage) => in_array($stage->id, $keptIds, true))
                ->each(fn (JourneyStage $stage) => $this->repository->delete($stage));

            $this->cache->forgetJourneyStages($data->productId);

            return $this->repository->forProduct($data->productId);
        });
    }

    /**
     * Pad existing stages to five slots for the product journey editor.
     *
     * @return list<array{id: ?int, title: string, text: string, imageUrl: ?string}>
     */
    public function editorSlotsForProduct(int $productId): array
    {
        $stages = $this->repository->forProduct($productId)->values();
        $slots = [];

        for ($i = 0; $i < SyncProductJourneyData::STEP_COUNT; $i++) {
            $stage = $stages->get($i);

            $slots[] = [
                'id' => $stage?->id,
                'title' => $stage?->title ?? '',
                'text' => $stage?->text ?? '',
                'imageUrl' => $stage?->hasImage() ? $stage->imageUrl('medium') : null,
            ];
        }

        return $slots;
    }

    /**
     * @return array<int, array{title: string, text: string, image: ?string, imageAlt: string}>
     */
    public function forProduct(int $productId): array
    {
        return $this->cache->remember(
            $this->cacheKey($productId),
            self::CACHE_TTL,
            function () use ($productId) {
                return $this->repository->publishedForProduct($productId)
                    ->map(fn (JourneyStage $stage) => [
                        'title' => $stage->title,
                        'text' => $stage->text,
                        'image' => $stage->hasImage() ? $stage->imageUrl('medium') : null,
                        'imageAlt' => $stage->title,
                    ])
                    ->values()
                    ->all();
            }
        );
    }

    private function cacheKey(int $productId): string
    {
        return "journey.stages.product.{$productId}";
    }
}
