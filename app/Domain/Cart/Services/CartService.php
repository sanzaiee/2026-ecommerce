<?php

namespace App\Domain\Cart\Services;

use App\Domain\Cart\Models\CartItem;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Support\Store\StoreOwnerContext;
use App\Support\ViewData\StorefrontBasketMapper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function __construct(
        private ProductRepositoryInterface $products,
        private StorefrontBasketMapper $mapper,
    ) {}

    /**
     * @return array{items: list<array<string, mixed>>, subtotal: float, itemCount: int}
     */
    public function snapshot(StoreOwnerContext $owner): array
    {
        $items = $this->baseQuery($owner)
            ->with(['product.category', 'product.brand', 'product.media'])
            ->get();

        $rows = $items->map(fn (CartItem $item) => [
            'product' => $item->product,
            'quantity' => $item->quantity,
        ])->all();

        return $this->mapper->summarizeCart($rows);
    }

    public function add(StoreOwnerContext $owner, Product $product, int $quantity = 1): array
    {
        if (! $product->hasStockFor($quantity)) {
            throw ValidationException::withMessages([
                'product' => ['Insufficient stock. Only '.$product->stock_quantity.' available.'],
            ]);
        }

        $quantity = max(1, min(99, $quantity));

        $item = $this->baseQuery($owner)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $newQuantity = min(99, $item->quantity + $quantity);

            if (! $product->hasStockFor($newQuantity)) {
                throw ValidationException::withMessages([
                    'product' => ['Insufficient stock. Only '.$product->stock_quantity.' available.'],
                ]);
            }

            $item->update([
                'quantity' => $newQuantity,
            ]);
        } else {
            $this->baseQuery($owner)->create([
                ...$this->ownerAttributes($owner),
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return $this->snapshot($owner);
    }

    public function setQuantity(StoreOwnerContext $owner, Product $product, int $quantity): array
    {
        $item = $this->baseQuery($owner)
            ->where('product_id', $product->id)
            ->first();

        if (! $item) {
            throw ValidationException::withMessages([
                'product' => ['Item is not in your cart.'],
            ]);
        }

        if ($quantity < 1) {
            $item->delete();

            return $this->snapshot($owner);
        }

        if (! $product->hasStockFor($quantity)) {
            throw ValidationException::withMessages([
                'product' => ['Insufficient stock. Only '.$product->stock_quantity.' available.'],
            ]);
        }

        $item->update(['quantity' => min(99, $quantity)]);

        return $this->snapshot($owner);
    }

    public function remove(StoreOwnerContext $owner, Product $product): array
    {
        $this->baseQuery($owner)
            ->where('product_id', $product->id)
            ->delete();

        return $this->snapshot($owner);
    }

    public function clear(StoreOwnerContext $owner): void
    {
        $this->baseQuery($owner)->delete();
    }

    public function mergeGuestIntoUser(string $guestToken, int $userId): void
    {
        if ($guestToken === '') {
            return;
        }

        DB::transaction(function () use ($guestToken, $userId) {
            $guestItems = CartItem::query()
                ->where('guest_token', $guestToken)
                ->whereNull('user_id')
                ->get();

            foreach ($guestItems as $guestItem) {
                $userItem = CartItem::query()
                    ->where('user_id', $userId)
                    ->where('product_id', $guestItem->product_id)
                    ->first();

                if ($userItem) {
                    $userItem->update([
                        'quantity' => min(99, $userItem->quantity + $guestItem->quantity),
                    ]);
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

    /**
     * @param  list<array{id: string, qty: int}>  $lines
     */
    public function syncFromClient(StoreOwnerContext $owner, array $lines): array
    {
        foreach ($lines as $line) {
            $product = $this->products->findBySlug($line['id'] ?? '');

            if (! $product) {
                continue;
            }

            $qty = max(1, min(99, (int) ($line['qty'] ?? 1)));

            $existing = $this->baseQuery($owner)
                ->where('product_id', $product->id)
                ->first();

            if ($existing) {
                $existing->update(['quantity' => $qty]);
            } else {
                $this->baseQuery($owner)->create([
                    ...$this->ownerAttributes($owner),
                    'product_id' => $product->id,
                    'quantity' => $qty,
                ]);
            }
        }

        return $this->snapshot($owner);
    }

    /** @return Builder<CartItem> */
    private function baseQuery(StoreOwnerContext $owner): Builder
    {
        $query = CartItem::query();

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
