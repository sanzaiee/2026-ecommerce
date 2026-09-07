<?php

namespace App\Support\ViewData\DTOs;

/**
 * Page-level SEO payload for storefront views.
 */
class Seo
{
    /**
     * @param  array<int, string>|null  $keywords
     */
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly ?array $keywords = null,
        public readonly string $canonicalPath = '/',
        public readonly string $ogType = 'website',
        public readonly ?string $image = null,
    ) {}

    /**
     * Absolute canonical URL built from the canonical path.
     */
    public function canonicalUrl(): string
    {
        return rtrim(config('app.url'), '/').$this->canonicalPath;
    }

    /**
     * Absolute URL for the social share image, when set.
     */
    public function ogImageUrl(): ?string
    {
        if ($this->image === null || $this->image === '') {
            return null;
        }

        return str_starts_with($this->image, 'http')
            ? $this->image
            : rtrim(config('app.url'), '/').$this->image;
    }
}
