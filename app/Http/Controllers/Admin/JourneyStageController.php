<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Journey\DTOs\JourneyStageFilterData;
use App\Domain\Journey\Services\JourneyStageService;
use App\Domain\Product\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Journey\StoreJourneyStageRequest;
use App\Http\Requests\Journey\SyncProductJourneyRequest;
use App\Http\Requests\Journey\UpdateJourneyStageRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JourneyStageController extends Controller
{
    public function __construct(
        private JourneyStageService $stages,
        private ProductService $products,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);

        return view('admin.journey-stages.index', [
            'stages' => $this->stages->paginateForAdmin($filters),
            'filters' => $filters,
            'products' => $this->stages->productOptionsForForm(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.journey-stages.form', [
            'stage' => null,
            'products' => $this->stages->productOptionsForForm(),
            'selectedProductId' => $request->integer('product_id') ?: null,
        ]);
    }

    public function store(StoreJourneyStageRequest $request): RedirectResponse
    {
        $stage = $this->stages->create($request->toDto());

        return redirect()
            ->route('admin.journey-stages.index', ['product_id' => $stage->product_id])
            ->with('status', 'Journey stage created successfully.');
    }

    public function edit(int $journey_stage): View
    {
        $model = $this->stages->findById($journey_stage);

        if (! $model) {
            abort(404);
        }

        return view('admin.journey-stages.form', [
            'stage' => $model,
            'products' => $this->stages->productOptionsForForm(),
            'selectedProductId' => $model->product_id,
        ]);
    }

    public function update(UpdateJourneyStageRequest $request, int $journey_stage): RedirectResponse
    {
        $stage = $this->stages->update($request->toDto());

        return redirect()
            ->route('admin.journey-stages.index', ['product_id' => $stage->product_id])
            ->with('status', 'Journey stage updated successfully.');
    }

    public function destroy(int $journey_stage): RedirectResponse
    {
        $model = $this->stages->findById($journey_stage);
        $productId = $model?->product_id;

        $this->stages->delete($journey_stage);

        return redirect()
            ->route('admin.journey-stages.index', array_filter(['product_id' => $productId]))
            ->with('status', 'Journey stage deleted.');
    }

    public function destroyImage(int $journey_stage): RedirectResponse
    {
        $model = $this->stages->findById($journey_stage);

        if (! $model) {
            abort(404);
        }

        $this->stages->removeImage($journey_stage);

        return redirect()->route('admin.journey-stages.edit', $journey_stage)
            ->with('status', 'Journey stage image removed.');
    }

    public function editForProduct(int $product): View
    {
        $model = $this->products->findById($product);

        if (! $model || $model->trashed()) {
            abort(404);
        }

        $this->authorize('update', $model);

        return view('admin.products.journey', [
            'product' => $model,
            'steps' => $this->stages->editorSlotsForProduct($model->id),
        ]);
    }

    public function syncForProduct(SyncProductJourneyRequest $request, int $product): RedirectResponse
    {
        $model = $this->products->findById($product);

        if (! $model || $model->trashed()) {
            abort(404);
        }

        $this->authorize('update', $model);

        $this->stages->syncForProduct($request->toDto($model->id));

        return redirect()
            ->route('admin.products.journey.edit', $model)
            ->with('status', 'Product journey saved successfully.');
    }

    private function filtersFromRequest(Request $request): JourneyStageFilterData
    {
        $search = trim($request->string('search')->toString());
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $published = $request->string('published')->toString();
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;

        return new JourneyStageFilterData(
            search: $search !== '' ? $search : null,
            productId: $request->filled('product_id') ? $request->integer('product_id') : null,
            published: in_array($published, ['published', 'draft'], true) ? $published : null,
            dateFrom: $dateFrom !== '' ? $dateFrom : null,
            dateTo: $dateTo !== '' ? $dateTo : null,
            perPage: $perPage,
            page: max(1, (int) $request->input('page', 1)),
        );
    }
}
