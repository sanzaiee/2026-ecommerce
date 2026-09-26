<?php

namespace App\Support\ViewData;

use App\Domain\Blog\DTOs\BlogStorefrontFilterData;
use App\Domain\Blog\Models\Blog;
use App\Domain\Blog\Models\BlogCategory;
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

    /**
     * SEO payload for the blog storefront listing/index page.
     */
    public function toIndexSeo(
        ?BlogCategory $category = null,
        ?BlogStorefrontFilterData $filters = null,
        ?string $featuredImage = null,
    ): Seo {
        $hasSearch = ! empty($filters?->search);

        if ($hasSearch) {
            $title = 'Search: "'.$filters->search.'" — Stories & Cultural Insights';
            $description = "Search results for \"{$filters->search}\" across traditional Newari stories, crafts, pottery, and cultural insights.";
            $canonicalPath = '/blog';
        } elseif ($category !== null) {
            $title = $category->name.' — Newari Traditions & Cultural Stories';
            $description = $category->description
                ?: "Explore stories, rituals, and living heritage under {$category->name} in our Newari cultural journal.";
            $canonicalPath = '/blog?category='.$category->slug;
        } else {
            $title = 'Stories & Cultural Heritage — Newari Traditions, Craft & Community';
            $description = 'A growing library on Newari life — professions passed down through families, festival rituals, courtyard culture, traditional food, and the stories elders still tell.';
            $canonicalPath = '/blog';
        }

        return new Seo(
            title: $title,
            description: $description,
            keywords: [
                'Newari traditions',
                'Kathmandu valley culture',
                'traditional Newari crafts',
                'pottery Thimi Nepal',
                'Newari festival rituals',
                'cultural heritage Nepal',
                'terracotta craft stories',
            ],
            canonicalPath: $canonicalPath,
            ogType: 'website',
            image: $featuredImage,
            noIndex: $hasSearch,
        );
    }
}
