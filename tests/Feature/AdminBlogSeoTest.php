<?php

use App\Domain\Blog\Models\Blog;
use App\Enums\UserRole;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
});

it('shows seo fields on the admin blog create form', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.blogs.create'))
        ->assertOk()
        ->assertSee('name="meta_title"', false)
        ->assertSee('name="meta_description"', false)
        ->assertSee('name="meta_keywords"', false);
});

it('persists seo fields and normalizes meta keywords on create', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.blogs.store'), [
            'title' => 'Thimi Potters Guide',
            'content' => '<p>Village ceramic traditions.</p>',
            'meta_title' => 'Thimi Potters Guide | Clay Arts',
            'meta_description' => 'A look at Newar pottery workshops in Thimi.',
            'meta_keywords' => 'Thimi pottery, Newar craft , clay traditions',
        ])
        ->assertRedirect(route('admin.blogs.index'));

    $this->assertDatabaseHas('blogs', [
        'title' => 'Thimi Potters Guide',
        'slug' => 'thimi-potters-guide',
        'meta_title' => 'Thimi Potters Guide | Clay Arts',
        'meta_description' => 'A look at Newar pottery workshops in Thimi.',
        'meta_keywords' => 'Thimi pottery, Newar craft, clay traditions',
    ]);
});

it('defaults meta title to the post title when left blank', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.blogs.store'), [
            'title' => 'Clay Water Filters',
            'content' => '<p>Traditional filters from Madhyapur.</p>',
        ])
        ->assertRedirect(route('admin.blogs.index'));

    $this->assertDatabaseHas('blogs', [
        'title' => 'Clay Water Filters',
        'meta_title' => 'Clay Water Filters',
        'meta_description' => null,
        'meta_keywords' => null,
    ]);
});

it('renders blog post seo meta on the storefront', function () {
    Blog::factory()->create([
        'title' => 'Potters of Thimi',
        'slug' => 'potters-of-thimi',
        'content' => '<p>Village ceramic traditions in flux.</p>',
        'excerpt' => 'Short card summary.',
        'meta_title' => 'The Potters of Thimi | Clay Traditions',
        'meta_description' => 'Newar pottery-making traditions in Thimi, Nepal.',
        'meta_keywords' => 'Thimi pottery, Newar craft',
    ]);

    $this->get(route('blog.show', 'potters-of-thimi'))
        ->assertOk()
        ->assertSee('<title>The Potters of Thimi | Clay Traditions</title>', false)
        ->assertSee('name="description" content="Newar pottery-making traditions in Thimi, Nepal."', false)
        ->assertSee('name="keywords" content="Thimi pottery, Newar craft"', false)
        ->assertSee('property="og:type" content="article"', false)
        ->assertSee('<link rel="canonical" href="'.config('app.url').'/blog/potters-of-thimi"', false);
});

it('falls back to title and excerpt for storefront seo when meta fields are empty', function () {
    Blog::factory()->create([
        'title' => 'Straw Kiln Firing',
        'slug' => 'straw-kiln-firing',
        'content' => '<p>Four-day traditional firings.</p>',
        'excerpt' => 'How Thimi potters fire straw kilns.',
        'meta_title' => null,
        'meta_description' => null,
        'meta_keywords' => null,
    ]);

    $this->get(route('blog.show', 'straw-kiln-firing'))
        ->assertOk()
        ->assertSee('<title>Straw Kiln Firing</title>', false)
        ->assertSee('name="description" content="How Thimi potters fire straw kilns."', false)
        ->assertDontSee('name="keywords"', false);
});
