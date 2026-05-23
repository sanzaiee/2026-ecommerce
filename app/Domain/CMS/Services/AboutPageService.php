<?php

namespace App\Domain\CMS\Services;

use App\Domain\CMS\DTOs\UpdateAboutPageData;
use App\Domain\CMS\Models\AboutPage;
use App\Domain\CMS\Repositories\AboutPageRepositoryInterface;
use App\Services\CacheService;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;

class AboutPageService
{
    private const CACHE_TTL = 3600;

    private const GALLERY_SLOTS = 3;

    private const CRAFT_SLOTS = 2;

    public function __construct(
        private AboutPageRepositoryInterface $repository,
        private FileUploadService $uploads,
        private CacheService $cache,
    ) {}

    public function getPublicPayload(): AboutPage
    {
        return $this->cache->remember('about.page', self::CACHE_TTL, fn () => $this->repository->getSingleton());
    }

    public function update(UpdateAboutPageData $data): AboutPage
    {
        $page = $this->repository->getSingleton();

        if ($data->toArray() !== []) {
            $page = $this->repository->update($page, $data->toArray());
        }

        $this->uploads->addSingle($page, 'hero', $data->heroImage);
        $this->uploads->addSingle($page, 'story', $data->storyImage);

        if ($data->galleryImages !== null || $data->galleryMeta !== null) {
            $this->syncGallerySlots($page, $data->galleryImages ?? [], $data->galleryMeta ?? []);
        }

        if ($data->craftImages !== null || $data->craftMeta !== null) {
            $this->syncCraftSlots($page, $data->craftImages ?? [], $data->craftMeta ?? []);
        }

        $this->cache->forgetAbout();

        return $this->repository->getSingleton();
    }

    /**
     * @param  array<int, UploadedFile>  $images
     * @param  array<int, array{alt: ?string, caption: ?string}>  $meta
     */
    private function syncGallerySlots(AboutPage $page, array $images, array $meta): void
    {
        for ($slot = 0; $slot < self::GALLERY_SLOTS; $slot++) {
            $file = $images[$slot] ?? null;
            $alt = $meta[$slot]['alt'] ?? null;
            $caption = $meta[$slot]['caption'] ?? null;
            $media = $page->mediaForSlot('gallery', $slot);

            if ($file instanceof UploadedFile) {
                $media?->delete();
                $page->addMedia($file)
                    ->withCustomProperties([
                        'slot' => $slot,
                        'alt' => $alt,
                        'caption' => $caption,
                    ])
                    ->toMediaCollection('gallery');
            } elseif ($media) {
                $media->setCustomProperty('slot', $slot);
                $media->setCustomProperty('alt', $alt);
                $media->setCustomProperty('caption', $caption);
                $media->save();
            }
        }
    }

    /**
     * @param  array<int, UploadedFile>  $images
     * @param  array<int, array{alt: ?string}>  $meta
     */
    private function syncCraftSlots(AboutPage $page, array $images, array $meta): void
    {
        for ($slot = 0; $slot < self::CRAFT_SLOTS; $slot++) {
            $file = $images[$slot] ?? null;
            $alt = $meta[$slot]['alt'] ?? null;
            $media = $page->mediaForSlot('craft', $slot);

            if ($file instanceof UploadedFile) {
                $media?->delete();
                $page->addMedia($file)
                    ->withCustomProperties([
                        'slot' => $slot,
                        'alt' => $alt,
                    ])
                    ->toMediaCollection('craft');
            } elseif ($media) {
                $media->setCustomProperty('slot', $slot);
                $media->setCustomProperty('alt', $alt);
                $media->save();
            }
        }
    }
}
