<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Brand\Services\BrandService;
use App\Domain\Category\Services\CategoryService;
use App\Domain\Product\DTOs\ProductFilterData;
use App\Domain\Product\Services\ProductService;
use App\Enums\StockStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $products,
        private CategoryService $categories,
        private BrandService $brands,
    ) {}

    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $visibility = $request->string('status')->toString();

        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;

        $filters = new ProductFilterData(
            search: $search !== '' ? $search : null,
            categoryId: $request->filled('category_id') ? $request->integer('category_id') : null,
            brandId: $request->filled('brand_id') ? $request->integer('brand_id') : null,
            stockStatus: $request->filled('stock_status') ? $request->string('stock_status')->toString() : null,
            visibility: in_array($visibility, ['trashed', 'all'], true) ? $visibility : null,
            dateFrom: $dateFrom !== '' ? $dateFrom : null,
            dateTo: $dateTo !== '' ? $dateTo : null,
            perPage: $perPage,
            page: max(1, (int) $request->input('page', 1)),
        );

        return view('admin.products.index', [
            'products' => $this->products->paginateForAdmin($filters),
            'filters' => $filters,
            'categories' => $this->categories->all(),
            'brands' => $this->brands->all(),
            'stockStatuses' => StockStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => null,
            'categories' => $this->categories->all(),
            'brands' => $this->brands->all(),
            'stockStatuses' => StockStatus::cases(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->products->create($request->toDto());

        return redirect()->route('admin.products.index')
            ->with('status', 'Product created successfully.');
    }

    public function edit(int $product): View
    {
        $model = $this->products->findById($product);

        if (! $model) {
            abort(404);
        }

        return view('admin.products.form', [
            'product' => $model,
            'categories' => $this->categories->all(),
            'brands' => $this->brands->all(),
            'stockStatuses' => StockStatus::cases(),
        ]);
    }

    public function update(UpdateProductRequest $request, int $product): RedirectResponse
    {
        $this->products->update($request->toDto());

        return redirect()->route('admin.products.index')
            ->with('status', 'Product updated successfully.');
    }

    public function destroy(int $product): RedirectResponse
    {
        $this->products->delete($product);

        return redirect()->route('admin.products.index')
            ->with('status', 'Product deleted.');
    }

    public function destroyMedia(int $product, int $media): RedirectResponse
    {
        $model = $this->products->findById($product);

        if (! $model) {
            abort(404);
        }

        $this->authorize('update', $model);

        try {
            $this->products->removeGalleryMedia($product, $media);
        } catch (\RuntimeException) {
            abort(404);
        }

        return redirect()->route('admin.products.edit', $product)
            ->with('status', 'Product image removed.');
    }
}
