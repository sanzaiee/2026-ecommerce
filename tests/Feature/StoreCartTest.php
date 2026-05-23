<?php

use App\Domain\Cart\Models\CartItem;
use App\Domain\Product\Models\Product;
use App\Domain\Wishlist\Models\WishlistItem;
use App\Enums\UserRole;
use App\Models\User;
use App\Support\Store\GuestStoreToken;
use Database\Seeders\CatalogSeeder;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

it('persists cart item for guest with cookie', function () {
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();

    $response = $this->postJson(route('store.cart.items.store'), [
        'id' => $product->slug,
        'qty' => 2,
    ]);

    $response->assertOk()
        ->assertJsonPath('itemCount', 2)
        ->assertCookie(GuestStoreToken::COOKIE_NAME);

    expect(CartItem::query()->where('product_id', $product->id)->value('quantity'))->toBe(2);
});

it('merges guest cart into user on login', function () {
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();
    $token = 'guest-merge-token-123456789012345678901234';

    CartItem::create([
        'guest_token' => $token,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $user = User::factory()->create([
        'email' => 'merge@example.com',
        'password' => bcrypt('password123'),
        'role' => UserRole::Customer,
    ]);

    $this->withCookie(GuestStoreToken::COOKIE_NAME, $token)
        ->post(route('login'), [
            'email' => 'merge@example.com',
            'password' => 'password123',
        ])
        ->assertRedirect(route('account'));

    expect(CartItem::query()->where('user_id', $user->id)->where('product_id', $product->id)->exists())
        ->toBeTrue();
    expect(CartItem::query()->where('guest_token', $token)->exists())->toBeFalse();
});

it('toggles wishlist item for logged-in customer', function () {
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $this->actingAs($customer)
        ->postJson(route('store.wishlist.items.store'), ['id' => $product->slug])
        ->assertOk()
        ->assertJsonPath('added', true);

    expect(WishlistItem::query()->where('user_id', $customer->id)->where('product_id', $product->id)->exists())
        ->toBeTrue();

    $this->actingAs($customer)
        ->postJson(route('store.wishlist.items.store'), ['id' => $product->slug])
        ->assertOk()
        ->assertJsonPath('added', false);

    expect(WishlistItem::query()->where('user_id', $customer->id)->where('product_id', $product->id)->exists())
        ->toBeFalse();
});

it('renders wishlisted product cards with active heart on shop page', function () {
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    WishlistItem::create([
        'user_id' => $customer->id,
        'product_id' => $product->id,
    ]);

    $this->actingAs($customer)
        ->get(route('shop'))
        ->assertOk()
        ->assertSee('data-product-id="'.$product->slug.'"', false)
        ->assertSee('bi-heart-fill', false)
        ->assertSee('aria-pressed="true"', false);
});
