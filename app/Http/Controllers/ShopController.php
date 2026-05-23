<?php

namespace App\Http\Controllers;

use App\Domain\Category\Services\CategoryService;
use App\Domain\Product\DTOs\ProductFilterData;
use App\Domain\Product\Services\ProductService;
use App\Support\ViewData\StorefrontProductMapper;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct(
        private ProductService $products,
        private CategoryService $categories,
        private StorefrontProductMapper $mapper,
    ) {}

    public function __invoke(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $filters = new ProductFilterData(
            categorySlug: $request->query('category'),
            search: $search !== '' ? $search : null,
            perPage: 50,
        );

        $paginator = $this->products->paginate($filters);

        return view('shop', [
            'cartTotal' => 'Rs. 0',
            'pageTitle' => $search !== '' ? 'Search results' : 'All Products',
            'activeSearch' => $search !== '' ? $search : null,
            'products' => $paginator->getCollection()->map(fn ($p) => $this->mapper->toCard($p))->all(),
            'filterCategories' => $this->categories->all(),
            'activeCategory' => $request->query('category'),
        ]);
    }
}
