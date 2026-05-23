<?php

namespace App\Domain\Wishlist\Services;

use App\Domain\Cart\Services\CartService;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Wishlist\Models\WishlistItem;
use App\Support\Store\StoreOwnerContext;
use App\Support\ViewData\StorefrontBasketMapper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class WishlistService
{
    public function __construct(
        private CartService $cart,
        private StorefrontBasketMapper $mapper,
        private ProductRepositoryInterface $products,
    ) {}

    /**
     * @return array{items: list<array<string, mixed>>, count: int}
     */
    /**
     * @return list<string>
     */
    public function productSlugs(StoreOwnerContext $owner): array
    {
        return $this->baseQuery($owner)
            ->join('products', 'products.id', '=', 'wishlist_items.product_id')
            ->orderBy('wishlist_items.id')
            ->pluck('products.slug')
            ->all();
    }

    public function snapshot(StoreOwnerContext $owner): array
    {
        $products = $this->baseQuery($owner)
            ->with(['product.category', 'product.brand', 'product.media'])
            ->get()
            ->pluck('product')
            ->filter();

        return $this->mapper->summarizeWishlist($products);
    }

    /**
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    public function paginate(StoreOwnerContext $owner, int $perPage = 5): LengthAwarePaginator
    {
        return $this->baseQuery($owner)
            ->with(['product.category', 'product.brand', 'product.media'])
            ->latest('wishlist_items.id')
            ->paginate($perPage)
            ->withQueryString()
            ->through(function (WishlistItem $item): ?array {
                if (! $item->product) {
                    return null;
                }

                return $this->mapper->lineFromProduct($item->product, 1);
            });
    }

    /**
     * @return array{added: bool, wishlist: array{items: list<array<string, mixed>>, count: int}}
     */
    public function toggle(StoreOwnerContext $owner, Product $product): array
    {
        $existing = $this->baseQuery($owner)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return [
                'added' => false,
                'wishlist' => $this->snapshot($owner),
            ];
        }

        $this->baseQuery($owner)->create([
            ...$this->ownerAttributes($owner),
            'product_id' => $product->id,
        ]);

        return [
            'added' => true,
            'wishlist' => $this->snapshot($owner),
        ];
    }

    public function remove(StoreOwnerContext $owner, Product $product): array
    {
        $this->baseQuery($owner)
            ->where('product_id', $product->id)
            ->delete();

        return $this->snapshot($owner);
    }

    /**
     * @return array{cart: array<string, mixed>, wishlist: array<string, mixed>}
     */
    public function moveToCart(StoreOwnerContext $owner, Product $product): array
    {
        $item = $this->baseQuery($owner)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $this->cart->add($owner, $product, 1);
            $item->delete();
        }

        return [
            'cart' => $this->cart->snapshot($owner),
            'wishlist' => $this->snapshot($owner),
        ];
    }

    /**
     * @return array{cart: array<string, mixed>, wishlist: array<string, mixed>}
     */
    public function moveAllToCart(StoreOwnerContext $owner): array
    {
        $items = $this->baseQuery($owner)
            ->with('product')
            ->get();

        foreach ($items as $item) {
            if ($item->product && $item->product->inStock()) {
                $this->cart->add($owner, $item->product, 1);
            }
        }

        $this->baseQuery($owner)->delete();

        return [
            'cart' => $this->cart->snapshot($owner),
            'wishlist' => $this->snapshot($owner),
        ];
    }

    /**
     * @param  list<string>  $slugs
     */
    public function syncFromClient(StoreOwnerContext $owner, array $slugs): array
    {
        foreach ($slugs as $slug) {
            $product = $this->products->findBySlug($slug);

            if (! $product) {
                continue;
            }

            $exists = $this->baseQuery($owner)
                ->where('product_id', $product->id)
                ->exists();

            if (! $exists) {
                $this->baseQuery($owner)->create([
                    ...$this->ownerAttributes($owner),
                    'product_id' => $product->id,
                ]);
            }
        }

        return $this->snapshot($owner);
    }

    public function mergeGuestIntoUser(string $guestToken, int $userId): void
    {
        if ($guestToken === '') {
            return;
        }

        DB::transaction(function () use ($guestToken, $userId) {
            $guestItems = WishlistItem::query()
                ->where('guest_token', $guestToken)
                ->whereNull('user_id')
                ->get();

            foreach ($guestItems as $guestItem) {
                $exists = WishlistItem::query()
                    ->where('user_id', $userId)
                    ->where('product_id', $guestItem->product_id)
                    ->exists();

                if ($exists) {
                    $guestItem->delete();
                } else {
                    $guestItem->update([
                        'user_id' => $userId,
                        'guest_token' => null,
                    ]);
                }
            }
        });
    }

    /** @return Builder<WishlistItem> */
    private function baseQuery(StoreOwnerContext $owner): Builder
    {
        $query = WishlistItem::query();

        if ($owner->isAuthenticated()) {
            return $query->where('user_id', $owner->user->id);
        }

        return $query
            ->whereNull('user_id')
            ->where('guest_token', $owner->guestToken);
    }

    /** @return array<string, mixed> */
    private function ownerAttributes(StoreOwnerContext $owner): array
    {
        if ($owner->isAuthenticated()) {
            return [
                'user_id' => $owner->user->id,
                'guest_token' => null,
            ];
        }

        return [
            'user_id' => null,
            'guest_token' => $owner->guestToken,
        ];
    }
}
