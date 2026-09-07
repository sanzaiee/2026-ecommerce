<?php

use App\Domain\Blog\DTOs\BlogStorefrontFilterData;
use App\Domain\Blog\DTOs\CreateBlogData;
use App\Domain\Blog\Models\Blog;
use App\Domain\Blog\Services\BlogService;

it('appends a suffix when two posts share a title slug', function () {
    $service = app(BlogService::class);

    $first = $service->create(new CreateBlogData(
        title: 'Fish Care Tips',
        content: '<p>First</p>',
        excerpt: null,
        blogCategoryId: null,
        position: 0,
        isFeatured: false,
    ));
    $second = $service->create(new CreateBlogData(
        title: 'Fish Care Tips',
        content: '<p>Second</p>',
        excerpt: null,
        blogCategoryId: null,
        position: 0,
        isFeatured: false,
    ));

    expect($first->slug)->toBe('fish-care-tips')
        ->and($second->slug)->toBe('fish-care-tips-1');
});

it('lists featured posts ahead of higher position posts', function () {
    Blog::factory()->create(['title' => 'Later', 'slug' => 'later', 'position' => 50, 'is_featured' => false]);
    Blog::factory()->featured()->create(['title' => 'Spotlight', 'slug' => 'spotlight', 'position' => 1]);

    $page = app(BlogService::class)->paginateForStorefront(new BlogStorefrontFilterData(perPage: 12, page: 1));

    expect($page->pluck('title')->all())->toBe(['Spotlight', 'Later']);
});

it('returns at most the requested number of latest posts', function () {
    Blog::factory()->count(5)->create();

    $latest = app(BlogService::class)->latestForStorefront(3);

    expect($latest)->toHaveCount(3)
        ->and($latest->pluck('title')->all())->toBe(
            Blog::query()->ordered()->limit(3)->pluck('title')->all()
        );
});
