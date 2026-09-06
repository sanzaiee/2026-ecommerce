<?php

use App\Domain\Blog\Models\Blog;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->customer = User::factory()->create(['role' => UserRole::Customer]);
});

it('redirects guests from the admin blog index', function () {
    $this->get(route('admin.blogs.index'))
        ->assertRedirect(route('admin.login'));
});

it('redirects customers away from the admin blog index', function () {
    $this->actingAs($this->customer)
        ->get(route('admin.blogs.index'))
        ->assertRedirect(route('admin.login'));
});

it('lets an admin view the blog index', function () {
    Blog::factory()->create(['title' => 'Drying mango at home']);

    $this->actingAs($this->admin)
        ->get(route('admin.blogs.index'))
        ->assertOk()
        ->assertSee('Drying mango at home');
});

it('loads the rich text editor on the blog create form', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.blogs.create'))
        ->assertOk()
        ->assertSee('data-rich-text', false)
        ->assertSee('class="form-control rich-text"', false)
        ->assertSee('admin-rich-text.js', false)
        ->assertDontSee('name="content" id="content" class="form-control rich-text" rows="12" required', false);
});

it('creates a blog post and generates a slug from the title', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.blogs.store'), [
            'title' => 'How We Dry Mango',
            'content' => '<p>Sun and patience.</p>',
            'position' => 4,
            'is_featured' => '1',
            'slug' => 'injected-slug',
        ])
        ->assertRedirect(route('admin.blogs.index'));

    $this->assertDatabaseHas('blogs', [
        'title' => 'How We Dry Mango',
        'slug' => 'how-we-dry-mango',
        'content' => '<p>Sun and patience.</p>',
        'position' => 4,
        'is_featured' => 1,
    ]);
});

it('rejects a blog post with missing required fields', function () {
    $this->actingAs($this->admin)
        ->from(route('admin.blogs.create'))
        ->post(route('admin.blogs.store'), [])
        ->assertRedirect(route('admin.blogs.create'))
        ->assertInvalid([
            'title' => 'The title field is required.',
            'content' => 'The content field is required.',
        ]);

    $this->assertDatabaseCount('blogs', 0);
});

it('rejects a position above the allowed range', function () {
    $this->actingAs($this->admin)
        ->from(route('admin.blogs.create'))
        ->post(route('admin.blogs.store'), [
            'title' => 'Out of range',
            'content' => '<p>Body</p>',
            'position' => 10000,
        ])
        ->assertRedirect(route('admin.blogs.create'))
        ->assertInvalid(['position']);

    $this->assertDatabaseCount('blogs', 0);
});

it('strips scripts from blog content on create', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.blogs.store'), [
            'title' => 'Safe Post',
            'content' => '<script>alert(1)</script><p>Hello</p>',
            'position' => 0,
        ])
        ->assertRedirect(route('admin.blogs.index'));

    $post = Blog::query()->where('title', 'Safe Post')->first();

    expect($post)->not->toBeNull()
        ->and($post->content)->toContain('<p>Hello</p>')
        ->and($post->content)->not->toContain('<script>');
});

it('does not let a customer create a blog post', function () {
    $this->actingAs($this->customer)
        ->post(route('admin.blogs.store'), [
            'title' => 'Should not save',
            'content' => '<p>Nope</p>',
        ])
        ->assertRedirect(route('admin.login'));

    $this->assertDatabaseCount('blogs', 0);
});

it('updates a blog post and regenerates the slug', function () {
    $post = Blog::factory()->create([
        'title' => 'Old Title',
        'slug' => 'old-title',
        'content' => '<p>Old</p>',
        'position' => 1,
        'is_featured' => false,
    ]);

    $this->actingAs($this->admin)
        ->put(route('admin.blogs.update', $post), [
            'title' => 'New Title',
            'content' => '<p>Updated</p>',
            'position' => 8,
            'is_featured' => '1',
        ])
        ->assertRedirect(route('admin.blogs.index'));

    expect($post->fresh())
        ->title->toBe('New Title')
        ->content->toBe('<p>Updated</p>')
        ->position->toBe(8)
        ->is_featured->toBeTrue()
        ->slug->toBe('new-title');
});

it('deletes a blog post', function () {
    $post = Blog::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.blogs.destroy', $post))
        ->assertRedirect(route('admin.blogs.index'));

    $this->assertModelMissing($post);
});

it('returns 404 when an admin updates a missing blog post', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.blogs.update', 999), [
            'title' => 'Gone',
            'content' => '<p>Gone</p>',
        ])
        ->assertNotFound();
});

it('stores a featured image when creating a blog post', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)
        ->post(route('admin.blogs.store'), [
            'title' => 'With Image',
            'content' => '<p>Body</p>',
            'image' => UploadedFile::fake()->image('cover.jpg', 800, 600),
        ])
        ->assertRedirect(route('admin.blogs.index'));

    $post = Blog::query()->where('slug', 'with-image')->first();

    expect($post)->not->toBeNull()
        ->and($post->hasImage())->toBeTrue();
});

it('rejects a non-image upload for the blog featured image', function () {
    $this->actingAs($this->admin)
        ->from(route('admin.blogs.create'))
        ->post(route('admin.blogs.store'), [
            'title' => 'Bad File',
            'content' => '<p>Body</p>',
            'image' => UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'),
        ])
        ->assertRedirect(route('admin.blogs.create'))
        ->assertInvalid(['image']);

    $this->assertDatabaseCount('blogs', 0);
});

it('removes a blog featured image', function () {
    Storage::fake('public');

    $post = Blog::factory()->create();
    $post->addMedia(UploadedFile::fake()->image('cover.jpg'))->toMediaCollection('blogs');

    expect($post->fresh()->hasImage())->toBeTrue();

    $this->actingAs($this->admin)
        ->delete(route('admin.blogs.image.destroy', $post))
        ->assertRedirect(route('admin.blogs.edit', $post));

    expect($post->fresh()->hasImage())->toBeFalse();
});
