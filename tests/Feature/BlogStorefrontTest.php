<?php

use App\Domain\Blog\Models\Blog;
use App\Domain\Blog\Models\BlogCategory;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('renders the blog listing ordered by featured then position', function () {
    Blog::factory()->create([
        'title' => 'Low position',
        'slug' => 'low-position',
        'position' => 1,
        'is_featured' => false,
    ]);
    Blog::factory()->featured()->create([
        'title' => 'Featured pick',
        'slug' => 'featured-pick',
        'position' => 0,
    ]);
    Blog::factory()->create([
        'title' => 'High position',
        'slug' => 'high-position',
        'position' => 10,
        'is_featured' => false,
    ]);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSeeTextInOrder([
            'Featured pick',
            'High position',
            'Low position',
        ])
        ->assertSee('Newari tradition')
        ->assertSee('blog-hub__grid', false);
});

it('filters blog posts by search query', function () {
    Blog::factory()->create([
        'title' => 'Mustard oil pickle guide',
        'slug' => 'mustard-oil-pickle-guide',
        'excerpt' => 'A traditional achar walkthrough.',
    ]);
    Blog::factory()->create([
        'title' => 'Unrelated snack notes',
        'slug' => 'unrelated-snack-notes',
        'excerpt' => 'Something else entirely.',
    ]);

    $this->get(route('blog.index', ['q' => 'mustard']))
        ->assertOk()
        ->assertSee('Mustard oil pickle guide')
        ->assertDontSee('Unrelated snack notes');
});

it('filters blog posts by category slug', function () {
    $recipes = BlogCategory::create([
        'name' => 'Traditional Recipes',
        'slug' => 'traditional-recipes',
        'description' => 'Recipe guides.',
        'sort_order' => 10,
    ]);
    $pickling = BlogCategory::create([
        'name' => 'Pickling',
        'slug' => 'pickling-preservation',
        'description' => 'Pickling guides.',
        'sort_order' => 20,
    ]);

    Blog::factory()->create([
        'title' => 'Dal bhat sides',
        'slug' => 'dal-bhat-sides',
        'blog_category_id' => $recipes->id,
    ]);
    Blog::factory()->create([
        'title' => 'Monsoon lime pickle',
        'slug' => 'monsoon-lime-pickle',
        'blog_category_id' => $pickling->id,
    ]);

    $this->get(route('blog.index', ['category' => 'traditional-recipes']))
        ->assertOk()
        ->assertSee('Dal bhat sides')
        ->assertDontSee('Monsoon lime pickle')
        ->assertSee('Traditional Recipes');
});

it('shows category links on a blog post page', function () {
    $category = BlogCategory::create([
        'name' => 'Seasonal Eating',
        'slug' => 'seasonal-eating',
        'description' => 'Seasonal guides.',
        'sort_order' => 5,
    ]);

    Blog::factory()->create([
        'title' => 'Winter pantry staples',
        'slug' => 'winter-pantry-staples',
        'content' => '<p>Store jars in a cool place.</p>',
        'blog_category_id' => $category->id,
    ]);

    $this->get(route('blog.show', 'winter-pantry-staples'))
        ->assertOk()
        ->assertSee('Seasonal Eating')
        ->assertSee(route('blog.index', ['category' => 'seasonal-eating']), false);
});

it('renders a blog post by slug', function () {
    Blog::factory()->create([
        'title' => 'Pickle season',
        'slug' => 'pickle-season',
        'content' => '<p>Mustard oil and sun.</p>',
    ]);

    $this->get(route('blog.show', 'pickle-season'))
        ->assertOk()
        ->assertSee('Pickle season')
        ->assertSee('Mustard oil and sun.', false);
});

it('returns 404 for an unknown blog slug', function () {
    $this->get(route('blog.show', 'missing-post'))
        ->assertNotFound();
});

it('does not render script tags from submitted blog content', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->post(route('admin.blogs.store'), [
            'title' => 'Public Safety',
            'content' => '<script>alert(1)</script><p>Clean copy</p>',
        ])
        ->assertRedirect(route('admin.blogs.index'));

    $this->get(route('blog.show', 'public-safety'))
        ->assertOk()
        ->assertSee('Clean copy')
        ->assertDontSee('<script>alert(1)</script>', false);
});

it('renders a blog post featured image on the storefront', function () {
    Storage::fake('public');

    $post = Blog::factory()->create([
        'title' => 'Cover story',
        'slug' => 'cover-story',
        'content' => '<p>Body</p>',
    ]);
    $post->addMedia(UploadedFile::fake()->image('cover.jpg'))->toMediaCollection('blogs');

    $this->get(route('blog.show', 'cover-story'))
        ->assertOk()
        ->assertSee('blog-show__image', false)
        ->assertSee($post->fresh()->imageUrl('medium'), false);
});
