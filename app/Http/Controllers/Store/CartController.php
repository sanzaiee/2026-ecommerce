<?php

namespace App\Http\Controllers\Store;

use App\Domain\Cart\Services\CartService;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\CartItemRequest;
use App\Http\Requests\Store\CartQuantityRequest;
use App\Http\Requests\Store\CartSyncRequest;
use App\Support\Store\GuestStoreToken;
use App\Support\Store\StoreOwnerContext;
use App\Support\Store\StoreOwnerResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cart,
        private ProductRepositoryInterface $products,
        private GuestStoreToken $guestToken,
        private StoreOwnerResolver $ownerResolver,
    ) {}

    public function show(Request $request): JsonResponse
    {
        return $this->jsonWithGuestCookie(
            $request,
            $this->cart->snapshot($this->owner($request))
        );
    }

    public function store(CartItemRequest $request): JsonResponse
    {
        $owner = $this->owner($request);
        $product = $this->products->findBySlug($request->productSlug());

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return $this->jsonWithGuestCookie(
            $request,
            $this->cart->add($owner, $product, $request->quantity())
        );
    }

    public function update(CartQuantityRequest $request, string $productSlug): JsonResponse
    {
        $owner = $this->owner($request);
        $product = $this->products->findBySlug($productSlug);

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return $this->jsonWithGuestCookie(
            $request,
            $this->cart->setQuantity($owner, $product, $request->quantity())
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
            $this->cart->remove($owner, $product)
        );
    }

    public function sync(CartSyncRequest $request): JsonResponse
    {
        return $this->jsonWithGuestCookie(
            $request,
            $this->cart->syncFromClient($this->owner($request), $request->lines())
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
