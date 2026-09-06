<?php

namespace App\Http\Controllers;

use App\Domain\Blog\DTOs\BlogStorefrontFilterData;
use App\Domain\Blog\Models\BlogCategory;
use App\Domain\Blog\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(private BlogService $blogs) {}

    public function index(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);
        $posts = $this->blogs->paginateForStorefront($filters);
        $categories = $this->blogs->categoriesForStorefront();
        $activeCategory = $categories->firstWhere('slug', $filters->category);

        return view('pages.blog-index', [
            'cartTotal' => 'Rs. 0',
            'pageTitle' => $activeCategory?->name ?? 'Blog',
            'posts' => $posts,
            'categories' => $categories,
            'filters' => $filters,
            'activeCategory' => $activeCategory,
        ]);
    }

    public function show(string $slug): View
    {
        $post = $this->blogs->findBySlug($slug);

        if (! $post) {
            abort(404);
        }

        $post->loadMissing('category');

        return view('pages.blog-show', [
            'cartTotal' => 'Rs. 0',
            'pageTitle' => $post->title,
            'post' => $post,
        ]);
    }

    private function filtersFromRequest(Request $request): BlogStorefrontFilterData
    {
        $search = trim($request->string('q')->toString());
        $category = trim($request->string('category')->toString());
        $featured = $request->string('featured')->toString();
        $perPage = (int) $request->input('per_page', 12);
        $perPage = in_array($perPage, [12, 24], true) ? $perPage : 12;

        if ($category !== '' && ! BlogCategory::query()->where('slug', $category)->exists()) {
            $category = '';
        }

        return new BlogStorefrontFilterData(
            search: $search !== '' ? $search : null,
            category: $category !== '' ? $category : null,
            featured: in_array($featured, ['featured', 'standard'], true) ? $featured : null,
            perPage: $perPage,
            page: max(1, (int) $request->input('page', 1)),
        );
    }
}
