<?php

namespace App\Support\Store;

use Illuminate\Http\Request;

class StoreOwnerResolver
{
    public function __construct(private GuestStoreToken $guestToken) {}

    public function fromRequest(Request $request): StoreOwnerContext
    {
        $user = $request->user();
        $user = $user?->isCustomer() ? $user : null;

        return new StoreOwnerContext(
            user: $user,
            guestToken: $this->guestToken->resolve($request),
        );
    }
}
