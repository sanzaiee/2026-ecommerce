<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Category\Services\CategoryService;
use App\Domain\CMS\Services\CMSService;
use App\Domain\Product\DTOs\ProductFilterData;
use App\Domain\Product\Services\ProductService;
use App\Enums\LandingProductSection;
use App\Http\Controllers\Controller;
use App\Http\Requests\CMS\UpdateLandingPageRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __construct(
        private CMSService $cms,
        private CategoryService $categories,
        private ProductService $products,
    ) {}

    public function edit(): View
    {
        $page = $this->cms->getPublicPayload();
        $allProducts = $this->products->paginateForAdmin(new ProductFilterData(perPage: 100))->getCollection();

        return view('admin.landing-page.edit', [
            'page' => $page,
            'categories' => $this->categories->all(),
            'products' => $allProducts,
            'selectedCategoryIds' => $page->featuredCategories->pluck('id')->all(),
            'everydayIds' => $page->productsForSection(LandingProductSection::Everyday)->pluck('products.id')->all(),
            'topSellingIds' => $page->productsForSection(LandingProductSection::TopSelling)->pluck('products.id')->all(),
        ]);
    }

    public function update(UpdateLandingPageRequest $request): RedirectResponse
    {
        $this->cms->update($request->toDto());

        return redirect()->route('admin.landing-page.edit')
            ->with('status', 'Landing page updated successfully.');
    }
}
