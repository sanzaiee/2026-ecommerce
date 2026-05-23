<?php

namespace App\Domain\CMS\DTOs;

use Illuminate\Http\UploadedFile;

readonly class UpdateAboutPageData
{
    /**
     * @param  array<int, array{icon: string, title: string, text: string}>|null  $values
     * @param  array<int, array{title: string, text: string}>|null  $processSteps
     * @param  array<int, array{alt: ?string, caption: ?string}>|null  $galleryMeta
     * @param  array<int, array{alt: ?string}>|null  $craftMeta
     * @param  array<int, UploadedFile>|null  $galleryImages
     * @param  array<int, UploadedFile>|null  $craftImages
     */
    public function __construct(
        public ?string $heroEyebrow = null,
        public ?string $heroLead = null,
        public ?string $storyHeading = null,
        public ?string $storyParagraph1 = null,
        public ?string $storyParagraph2 = null,
        public ?string $galleryHeading = null,
        public ?string $galleryLead = null,
        public ?string $processHeading = null,
        public ?array $values = null,
        public ?array $processSteps = null,
        public ?string $editorialBody = null,
        public ?string $ctaTitle = null,
        public ?string $ctaText = null,
        public ?string $metaTitle = null,
        public ?string $metaDescription = null,
        public ?UploadedFile $heroImage = null,
        public ?UploadedFile $storyImage = null,
        public ?array $galleryImages = null,
        public ?array $galleryMeta = null,
        public ?array $craftImages = null,
        public ?array $craftMeta = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'hero_eyebrow' => $this->heroEyebrow,
            'hero_lead' => $this->heroLead,
            'story_heading' => $this->storyHeading,
            'story_paragraph_1' => $this->storyParagraph1,
            'story_paragraph_2' => $this->storyParagraph2,
            'gallery_heading' => $this->galleryHeading,
            'gallery_lead' => $this->galleryLead,
            'process_heading' => $this->processHeading,
            'values' => $this->values,
            'process_steps' => $this->processSteps,
            'editorial_body' => $this->editorialBody,
            'cta_title' => $this->ctaTitle,
            'cta_text' => $this->ctaText,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
        ], fn ($v) => $v !== null);
    }
}
