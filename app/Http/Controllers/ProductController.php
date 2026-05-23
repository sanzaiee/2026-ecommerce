<?php

namespace App\Http\Controllers;

use App\Domain\Product\Services\ProductService;
use App\Domain\Review\Services\ReviewService;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Support\ViewData\StorefrontProductMapper;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $products,
        private ReviewService $reviews,
        private StorefrontProductMapper $mapper,
    ) {}

    public function show(string $slug): View
    {
        $product = $this->products->findBySlug($slug);

        if (! $product) {
            abort(404);
        }

        $related = $this->products->related($product)
            ->map(fn ($p) => $this->mapper->toRelated($p))
            ->all();

        $approvedReviews = $product->approvedReviews()
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'name' => $r->name,
                'rating' => $r->rating,
                'text' => $r->comment,
                'date' => $r->created_at?->format('Y-m-d'),
            ])
            ->all();

        return view('product', [
            'cartTotal' => 'Rs. 0',
            'product' => $this->mapper->toDetail($product),
            'relatedProducts' => $related,
            'reviews' => $approvedReviews,
        ]);
    }

    public function storeReview(StoreReviewRequest $request, string $slug): RedirectResponse
    {
        $product = $this->products->findBySlug($slug);

        if (! $product) {
            abort(404);
        }

        $this->reviews->create($request->toDto($product->id));

        return back()->with('status', 'Thank you! Your review is pending approval.');
    }
}
