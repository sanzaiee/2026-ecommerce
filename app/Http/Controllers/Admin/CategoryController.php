<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Category\DTOs\CategoryFilterData;
use App\Domain\Category\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categories) {}

    public function index(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);

        return view('admin.categories.index', [
            'categories' => $this->categories->paginateForAdmin($filters),
            'filters' => $filters,
        ]);
    }

    private function filtersFromRequest(Request $request): CategoryFilterData
    {
        $search = trim($request->string('search')->toString());
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $visibility = $request->string('status')->toString();
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;

        return new CategoryFilterData(
            search: $search !== '' ? $search : null,
            visibility: in_array($visibility, ['trashed', 'all'], true) ? $visibility : null,
            dateFrom: $dateFrom !== '' ? $dateFrom : null,
            dateTo: $dateTo !== '' ? $dateTo : null,
            perPage: $perPage,
            page: max(1, (int) $request->input('page', 1)),
        );
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => null]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categories->create($request->toDto());

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category created successfully.');
    }

    public function edit(int $category): View
    {
        $model = $this->categories->findById($category);

        if (! $model) {
            abort(404);
        }

        return view('admin.categories.form', ['category' => $model]);
    }

    public function update(UpdateCategoryRequest $request, int $category): RedirectResponse
    {
        $this->categories->update($request->toDto());

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category updated successfully.');
    }

    public function destroy(int $category): RedirectResponse
    {
        $this->categories->delete($category);

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category deleted.');
    }

    public function destroyImage(int $category): RedirectResponse
    {
        $model = $this->categories->findById($category);

        if (! $model) {
            abort(404);
        }

        $this->authorize('update', $model);
        $this->categories->removeImage($category);

        return redirect()->route('admin.categories.edit', $category)
            ->with('status', 'Category image removed.');
    }
}
