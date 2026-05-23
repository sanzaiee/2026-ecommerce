<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Review\DTOs\ReviewFilterData;
use App\Domain\Review\Services\ReviewService;
use App\Enums\ReviewStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $reviews) {}

    public function index(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);

        return view('admin.reviews.index', [
            'reviews' => $this->reviews->paginateForAdmin($filters),
            'filters' => $filters,
            'filterProducts' => $this->reviews->filterProductOptions(),
        ]);
    }

    private function filtersFromRequest(Request $request): ReviewFilterData
    {
        $page = max(1, (int) $request->input('page', 1));
        $productId = $request->filled('product_id') ? $request->integer('product_id') : null;

        if (! $request->has('status')) {
            return new ReviewFilterData(
                status: ReviewStatus::Pending,
                productId: $productId,
                page: $page,
            );
        }

        return new ReviewFilterData(
            status: $request->input('status') === ''
                ? null
                : $request->enum('status', ReviewStatus::class),
            hasStatusFilter: true,
            productId: $productId,
            page: $page,
        );
    }

    public function approve(int $id): RedirectResponse
    {
        $this->reviews->approve($id);

        return back()->with('status', 'Review approved.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->reviews->delete($id);

        return back()->with('status', 'Review deleted.');
    }
}
