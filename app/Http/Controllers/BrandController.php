<?php

namespace App\Http\Controllers;

use App\Domain\Brand\Services\BrandService;
use App\Domain\Product\DTOs\ProductFilterData;
use App\Domain\Product\Services\ProductService;
use App\Support\ViewData\StorefrontProductMapper;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(
        private BrandService $brands,
        private ProductService $products,
        private StorefrontProductMapper $mapper,
    ) {}

    public function show(string $slug): View
    {
        $brand = $this->brands->findBySlug($slug);

        if (! $brand) {
            abort(404);
        }

        $paginator = $this->products->paginate(new ProductFilterData(
            brandSlug: $slug,
            perPage: 50,
        ));

        $products = $paginator->getCollection()->map(fn ($p) => $this->mapper->toCard($p))->all();

        return view('brand', [
            'cartTotal' => 'Rs. 0',
            'brand' => $brand,
            'products' => $products,
            'productTotal' => $paginator->total(),
        ]);
    }
}
