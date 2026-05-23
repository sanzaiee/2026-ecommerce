<?php

namespace App\Concerns;

use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Multi-image gallery with thumbnail, medium, and large conversions.
 */
trait HasProductMediaGallery
{
    use ProvidesRelativeMediaUrls;

    protected function productMediaCollection(): string
    {
        return 'products';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this->productMediaCollection());
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(600)
            ->height(600)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(1200)
            ->height(1200)
            ->nonQueued();
    }

    public function hasImages(): bool
    {
        return $this->hasMedia($this->productMediaCollection());
    }

    public function primaryImageUrl(string $conversion = 'medium'): string
    {
        return $this->relativeMediaUrl(
            $this->getFirstMediaUrl($this->productMediaCollection(), $conversion)
        );
    }

    public function mediaPreviewUrl(Media $media, string $conversion = 'thumbnail'): string
    {
        $url = $media->hasGeneratedConversion($conversion)
            ? $media->getUrl($conversion)
            : $media->getUrl();

        return $this->relativeMediaUrl($url);
    }

    /**
     * @return Collection<int, Media>
     */
    public function galleryMedia(): Collection
    {
        return $this->getMedia($this->productMediaCollection());
    }

    /**
     * @return array<int, string>
     */
    public function galleryUrls(string $conversion = 'large'): array
    {
        return $this->galleryMedia()
            ->map(fn (Media $media) => $this->mediaPreviewUrl($media, $conversion))
            ->values()
            ->all();
    }
}
