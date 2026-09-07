<?php

namespace App\Domain\Blog\Services;

use App\Domain\Blog\DTOs\BlogFilterData;
use App\Domain\Blog\DTOs\BlogStorefrontFilterData;
use App\Domain\Blog\DTOs\CreateBlogData;
use App\Domain\Blog\DTOs\UpdateBlogData;
use App\Domain\Blog\Models\Blog;
use App\Domain\Blog\Models\BlogCategory;
use App\Domain\Blog\Repositories\BlogRepositoryInterface;
use App\Services\CacheService;
use App\Services\FileUploadService;
use App\Services\HtmlSanitizer;
use App\Services\SEOService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use RuntimeException;

class BlogService
{
    private const CACHE_TTL = 3600;

    private const MEDIA_COLLECTION = 'blogs';

    public function __construct(
        private BlogRepositoryInterface $repository,
        private SEOService $seo,
        private CacheService $cache,
        private HtmlSanitizer $sanitizer,
        private FileUploadService $uploads,
    ) {}

    public function paginateForAdmin(BlogFilterData $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($filters);
    }

    public function paginateForStorefront(BlogStorefrontFilterData $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForStorefront($filters);
    }

    /**
     * @return Collection<int, BlogCategory>
     */
    public function categoriesForStorefront(): Collection
    {
        return $this->repository->categoriesForStorefront();
    }

    /**
     * Latest posts for the landing page stories section.
     *
     * @return Collection<int, Blog>
     */
    public function latestForStorefront(int $limit = 3): Collection
    {
        return $this->repository->latestForStorefront($limit);
    }

    public function findById(int $id): ?Blog
    {
        return $this->repository->findById($id);
    }

    public function findBySlug(string $slug): ?Blog
    {
        return $this->cache->remember('blogs.'.$slug, self::CACHE_TTL, function () use ($slug) {
            return $this->repository->findBySlug($slug);
        });
    }

    public function create(CreateBlogData $data): Blog
    {
        $attributes = $this->sanitize($data->toArray());
        $attributes['slug'] = $this->uniqueSlug($attributes['title']);

        $blog = $this->repository->create($attributes);
        $this->uploads->addSingle($blog, self::MEDIA_COLLECTION, $data->image);
        $this->cache->forgetBlogs($blog->slug);

        return $blog->fresh('media');
    }

    public function update(UpdateBlogData $data): Blog
    {
        $blog = $this->repository->findById($data->id);

        if (! $blog) {
            throw new RuntimeException('Blog post not found.');
        }

        $previousSlug = $blog->slug;
        $attributes = $this->sanitize($data->toArray());
        $attributes['slug'] = $this->uniqueSlug($attributes['title'], $blog->id);

        $blog = $this->repository->update($blog, $attributes);
        $this->uploads->addSingle($blog, self::MEDIA_COLLECTION, $data->image);
        $this->cache->forgetBlogs($previousSlug);

        if ($blog->slug !== $previousSlug) {
            $this->cache->forgetBlogs($blog->slug);
        }

        return $blog->fresh('media');
    }

    public function removeImage(int $id): Blog
    {
        $blog = $this->repository->findById($id);

        if (! $blog) {
            throw new RuntimeException('Blog post not found.');
        }

        $this->uploads->clearCollection($blog, self::MEDIA_COLLECTION);
        $this->cache->forgetBlogs($blog->slug);

        return $blog->fresh('media');
    }

    public function delete(int $id): void
    {
        $blog = $this->repository->findById($id);

        if (! $blog) {
            throw new RuntimeException('Blog post not found.');
        }

        $slug = $blog->slug;
        $this->repository->delete($blog);
        $this->cache->forgetBlogs($slug);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function sanitize(array $attributes): array
    {
        $attributes['title'] = trim(strip_tags((string) $attributes['title']));
        $attributes['content'] = $this->sanitizer->clean((string) $attributes['content']) ?? '';

        if (array_key_exists('excerpt', $attributes)) {
            $attributes['excerpt'] = trim(strip_tags((string) $attributes['excerpt']));
            $attributes['excerpt'] = $attributes['excerpt'] !== '' ? $attributes['excerpt'] : null;
        }

        return $attributes;
    }

    private function uniqueSlug(string $title, ?int $exceptId = null): string
    {
        $slug = $this->seo->slugify($title);
        $original = $slug !== '' ? $slug : 'post';
        $slug = $original;
        $i = 1;

        while ($this->repository->slugExists($slug, $exceptId)) {
            $slug = $original.'-'.$i++;
        }

        return $slug;
    }
}
