<?php

use App\Domain\Blog\Models\Blog;
use Database\Seeders\BlogCategorySeeder;
use Database\Seeders\BlogSeeder;

it('seeds blogs with schema-aligned fields and timestamps', function () {
    $this->seed(BlogCategorySeeder::class);
    $this->seed(BlogSeeder::class);

    expect(Blog::query()->count())->toBe(8);

    $featured = Blog::query()->where('slug', 'newari-achar-differences')->first();

    expect($featured)
        ->not->toBeNull()
        ->title->toBe('What Makes Newari Achar Different from Everyday Pickles')
        ->excerpt->toContain('Newari pickle traditions')
        ->position->toBe(8)
        ->is_featured->toBeTrue()
        ->blog_category_id->not->toBeNull()
        ->created_at->not->toBeNull()
        ->updated_at->not->toBeNull();

    $this->seed(BlogSeeder::class);

    expect(Blog::query()->count())->toBe(8);
});
