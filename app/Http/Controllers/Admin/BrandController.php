<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Brand\DTOs\BrandFilterData;
use App\Domain\Brand\Services\BrandService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(private BrandService $brands) {}

    public function index(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);

        return view('admin.brands.index', [
            'brands' => $this->brands->paginateForAdmin($filters),
            'filters' => $filters,
        ]);
    }

    private function filtersFromRequest(Request $request): BrandFilterData
    {
        $search = trim($request->string('search')->toString());
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $visibility = $request->string('status')->toString();
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;

        return new BrandFilterData(
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
        return view('admin.brands.form', ['brand' => null]);
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $this->brands->create($request->toDto());

        return redirect()->route('admin.brands.index')
            ->with('status', 'Brand created successfully.');
    }

    public function edit(int $brand): View
    {
        $model = $this->brands->findById($brand);

        if (! $model) {
            abort(404);
        }

        return view('admin.brands.form', ['brand' => $model]);
    }

    public function update(UpdateBrandRequest $request, int $brand): RedirectResponse
    {
        $this->brands->update($request->toDto());

        return redirect()->route('admin.brands.index')
            ->with('status', 'Brand updated successfully.');
    }

    public function destroy(int $brand): RedirectResponse
    {
        $this->brands->delete($brand);

        return redirect()->route('admin.brands.index')
            ->with('status', 'Brand deleted.');
    }

    public function destroyImage(int $brand): RedirectResponse
    {
        $model = $this->brands->findById($brand);

        if (! $model) {
            abort(404);
        }

        $this->authorize('update', $model);
        $this->brands->removeImage($brand);

        return redirect()->route('admin.brands.edit', $brand)
            ->with('status', 'Brand image removed.');
    }
}
