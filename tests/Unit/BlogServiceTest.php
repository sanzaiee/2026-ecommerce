<?php

use App\Domain\Blog\DTOs\CreateBlogData;
use App\Domain\Blog\Models\Blog;
use App\Domain\Blog\Services\BlogService;

it('appends a suffix when two posts share a title slug', function () {
    $service = app(BlogService::class);

    $first = $service->create(new CreateBlogData(
        title: 'Fish Care Tips',
        content: '<p>First</p>',
        position: 0,
        isFeatured: false,
    ));
    $second = $service->create(new CreateBlogData(
        title: 'Fish Care Tips',
        content: '<p>Second</p>',
        position: 0,
        isFeatured: false,
    ));

    expect($first->slug)->toBe('fish-care-tips')
        ->and($second->slug)->toBe('fish-care-tips-1');
});

it('lists featured posts ahead of higher position posts', function () {
    Blog::factory()->create(['title' => 'Later', 'slug' => 'later', 'position' => 50, 'is_featured' => false]);
    Blog::factory()->featured()->create(['title' => 'Spotlight', 'slug' => 'spotlight', 'position' => 1]);

    $page = app(BlogService::class)->paginateForStorefront(12, 1);

    expect($page->pluck('title')->all())->toBe(['Spotlight', 'Later']);
});
