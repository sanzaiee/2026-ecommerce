<?php

namespace App\Domain\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AboutPage extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'hero_eyebrow',
        'hero_lead',
        'story_heading',
        'story_paragraph_1',
        'story_paragraph_2',
        'gallery_heading',
        'gallery_lead',
        'process_heading',
        'values',
        'process_steps',
        'editorial_body',
        'cta_title',
        'cta_text',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'values' => 'array',
            'process_steps' => 'array',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero')->singleFile();
        $this->addMediaCollection('story')->singleFile();
        $this->addMediaCollection('gallery');
        $this->addMediaCollection('craft');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('large')
            ->width(1200)
            ->height(800)
            ->nonQueued();
    }

    public function mediaForSlot(string $collection, int $slot): ?Media
    {
        $items = $this->getMedia($collection);

        $match = $items->first(
            fn (Media $media) => (int) $media->getCustomProperty('slot') === $slot,
        );

        if ($match) {
            return $match;
        }

        return $items->sortBy('order_column')->values()->get($slot);
    }

    public function mediaUrl(Media $media): string
    {
        if ($media->hasGeneratedConversion('large')) {
            return $media->getUrl('large');
        }

        return $media->getUrl();
    }
}
