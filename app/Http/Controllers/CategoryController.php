<?php

namespace App\Http\Controllers;

use App\Domain\Category\Services\CategoryService;
use App\Domain\Product\DTOs\ProductFilterData;
use App\Domain\Product\Services\ProductService;
use App\Support\ViewData\StorefrontProductMapper;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categories,
        private ProductService $products,
        private StorefrontProductMapper $mapper,
    ) {}

    public function show(Request $request, string $slug): View
    {
        $category = $this->categories->findBySlug($slug);

        if (! $category) {
            abort(404);
        }

        $paginator = $this->products->paginate(new ProductFilterData(
            categorySlug: $slug,
            perPage: 50,
        ));

        $products = $paginator->getCollection()->map(fn ($p) => $this->mapper->toCard($p))->all();

        return view('category', [
            'cartTotal' => 'Rs. 0',
            'category' => $category,
            'products' => $products,
            'productTotal' => $paginator->total(),
        ]);
    }
}
