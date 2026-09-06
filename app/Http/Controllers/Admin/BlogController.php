<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Blog\DTOs\BlogFilterData;
use App\Domain\Blog\Models\BlogCategory;
use App\Domain\Blog\Services\BlogService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\StoreBlogRequest;
use App\Http\Requests\Blog\UpdateBlogRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(private BlogService $blogs) {}

    public function index(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);

        return view('admin.blogs.index', [
            'blogs' => $this->blogs->paginateForAdmin($filters),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.blogs.form', [
            'blog' => null,
            'categories' => BlogCategory::query()->ordered()->get(),
        ]);
    }

    public function store(StoreBlogRequest $request): RedirectResponse
    {
        $this->blogs->create($request->toDto());

        return redirect()->route('admin.blogs.index')
            ->with('status', 'Blog post created successfully.');
    }

    public function edit(int $blog): View
    {
        $model = $this->blogs->findById($blog);

        if (! $model) {
            abort(404);
        }

        return view('admin.blogs.form', [
            'blog' => $model,
            'categories' => BlogCategory::query()->ordered()->get(),
        ]);
    }

    public function update(UpdateBlogRequest $request, int $blog): RedirectResponse
    {
        $model = $this->blogs->findById($blog);

        if (! $model) {
            abort(404);
        }

        $this->blogs->update($request->toDto());

        return redirect()->route('admin.blogs.index')
            ->with('status', 'Blog post updated successfully.');
    }

    public function destroy(int $blog): RedirectResponse
    {
        $model = $this->blogs->findById($blog);

        if (! $model) {
            abort(404);
        }

        $this->authorize('delete', $model);
        $this->blogs->delete($blog);

        return redirect()->route('admin.blogs.index')
            ->with('status', 'Blog post deleted.');
    }

    public function destroyImage(int $blog): RedirectResponse
    {
        $model = $this->blogs->findById($blog);

        if (! $model) {
            abort(404);
        }

        $this->authorize('update', $model);
        $this->blogs->removeImage($blog);

        return redirect()->route('admin.blogs.edit', $blog)
            ->with('status', 'Blog image removed.');
    }

    private function filtersFromRequest(Request $request): BlogFilterData
    {
        $search = trim($request->string('search')->toString());
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $featured = $request->string('featured')->toString();
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;

        return new BlogFilterData(
            search: $search !== '' ? $search : null,
            featured: in_array($featured, ['featured', 'standard'], true) ? $featured : null,
            dateFrom: $dateFrom !== '' ? $dateFrom : null,
            dateTo: $dateTo !== '' ? $dateTo : null,
            perPage: $perPage,
            page: max(1, (int) $request->input('page', 1)),
        );
    }
}
