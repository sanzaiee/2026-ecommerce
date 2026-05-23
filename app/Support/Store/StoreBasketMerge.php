<?php

namespace App\Support\Store;

use App\Domain\Cart\Services\CartService;
use App\Domain\Wishlist\Services\WishlistService;
use App\Models\User;
use Illuminate\Http\Request;

class StoreBasketMerge
{
    public function __construct(
        private CartService $cart,
        private WishlistService $wishlist,
        private GuestStoreToken $guestToken,
    ) {}

    public function mergeGuestIntoUser(Request $request, User $user): void
    {
        if (! $user->isCustomer()) {
            return;
        }

        $token = $this->guestToken->resolve($request);

        $this->cart->mergeGuestIntoUser($token, $user->id);
        $this->wishlist->mergeGuestIntoUser($token, $user->id);
    }
}
