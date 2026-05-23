<?php

namespace App\Concerns;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Single-file image collection with standard thumbnail and medium conversions.
 *
 * Implement singleMediaCollection() on the model (e.g. return 'brands').
 */
trait HasSingleMediaImage
{
    use ProvidesRelativeMediaUrls;

    abstract protected function singleMediaCollection(): string;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this->singleMediaCollection())->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(300)
            ->height(300)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(600)
            ->height(600)
            ->nonQueued();
    }

    public function hasImage(): bool
    {
        return $this->hasMedia($this->singleMediaCollection());
    }

    public function imageUrl(string $conversion = 'medium'): string
    {
        return $this->relativeMediaUrl(
            $this->getFirstMediaUrl($this->singleMediaCollection(), $conversion)
        );
    }
}
