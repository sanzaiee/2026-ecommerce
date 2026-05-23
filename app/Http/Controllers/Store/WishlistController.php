<?php

namespace App\Http\Controllers\Store;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Wishlist\Services\WishlistService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\WishlistItemRequest;
use App\Http\Requests\Store\WishlistSyncRequest;
use App\Support\Store\GuestStoreToken;
use App\Support\Store\StoreOwnerContext;
use App\Support\Store\StoreOwnerResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct(
        private WishlistService $wishlist,
        private ProductRepositoryInterface $products,
        private GuestStoreToken $guestToken,
        private StoreOwnerResolver $ownerResolver,
    ) {}

    public function show(Request $request): JsonResponse|RedirectResponse|View
    {
        $owner = $this->owner($request);

        if ($request->expectsJson()) {
            return $this->jsonWithGuestCookie(
                $request,
                $this->wishlist->snapshot($owner)
            );
        }

        if ($owner->isAuthenticated()) {
            return redirect()->route('account.wishlist');
        }

        return view('store.wishlist', [
            'wishlist' => $this->wishlist->snapshot($owner),
            'cartTotal' => 'Rs. 0',
        ]);
    }

    public function store(WishlistItemRequest $request): JsonResponse
    {
        $owner = $this->owner($request);
        $product = $this->products->findBySlug($request->productSlug());

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return $this->jsonWithGuestCookie(
            $request,
            $this->wishlist->toggle($owner, $product)
        );
    }

    public function destroy(Request $request, string $productSlug): JsonResponse
    {
        $owner = $this->owner($request);
        $product = $this->products->findBySlug($productSlug);

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return $this->jsonWithGuestCookie(
            $request,
            $this->wishlist->remove($owner, $product)
        );
    }

    public function moveToCart(Request $request, string $productSlug): JsonResponse
    {
        $owner = $this->owner($request);
        $product = $this->products->findBySlug($productSlug);

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return $this->jsonWithGuestCookie(
            $request,
            $this->wishlist->moveToCart($owner, $product)
        );
    }

    public function moveAllToCart(Request $request): JsonResponse
    {
        return $this->jsonWithGuestCookie(
            $request,
            $this->wishlist->moveAllToCart($this->owner($request))
        );
    }

    public function sync(WishlistSyncRequest $request): JsonResponse
    {
        return $this->jsonWithGuestCookie(
            $request,
            $this->wishlist->syncFromClient($this->owner($request), $request->slugs())
        );
    }

    private function owner(Request $request): StoreOwnerContext
    {
        return $this->ownerResolver->fromRequest($request);
    }

    /** @param  array<string, mixed>  $payload */
    private function jsonWithGuestCookie(Request $request, array $payload): JsonResponse
    {
        $owner = $this->owner($request);
        $response = response()->json($payload);

        if (! $owner->isAuthenticated()) {
            $response->withCookie($this->guestToken->remember($owner->guestToken));
        }

        return $response;
    }
}
