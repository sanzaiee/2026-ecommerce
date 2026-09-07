<?php

namespace App\Support\ViewData;

use App\Domain\Blog\Models\Blog;
use App\Support\ViewData\DTOs\Seo;

class BlogMapper
{
    /**
     * Per-post SEO for the storefront blog show page.
     */
    public function toSeo(Blog $blog): Seo
    {
        $image = $blog->hasImage() ? $blog->imageUrl('medium') : null;

        return new Seo(
            title: $blog->meta_title ?: $blog->title,
            description: $blog->meta_description ?: $blog->summary(),
            keywords: $blog->meta_keywords
                ? array_values(array_filter(array_map('trim', explode(',', $blog->meta_keywords))))
                : null,
            canonicalPath: '/blog/'.$blog->slug,
            ogType: 'article',
            image: $image !== '' ? $image : null,
        );
    }
}
