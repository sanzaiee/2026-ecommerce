<?php

use App\Domain\Cart\Models\CartItem;
use App\Domain\Order\Models\Order;
use App\Domain\Product\Models\Product;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\CatalogSeeder;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

it('redirects guests to login when accessing checkout', function () {
    $this->get(route('store.checkout.show'))
        ->assertRedirect(route('login'));
});

it('redirects checkout to shop when cart is empty for customer', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $this->actingAs($customer)
        ->get(route('store.checkout.show'))
        ->assertRedirect(route('shop'));
});

it('prefills checkout with saved customer address', function () {
    $customer = User::factory()->create([
        'role' => UserRole::Customer,
        'phone' => '9811111111',
        'shipping_address_line1' => 'Baneshwor',
        'shipping_city' => 'Kathmandu',
        'shipping_district' => 'Kathmandu',
    ]);
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();

    $this->actingAs($customer)
        ->postJson(route('store.cart.items.store'), [
            'id' => $product->slug,
            'qty' => 1,
        ])
        ->assertOk();

    $this->actingAs($customer)
        ->get(route('store.checkout.show'))
        ->assertOk()
        ->assertSee('value="9811111111"', false)
        ->assertSee('value="Baneshwor"', false)
        ->assertSee('value="Kathmandu"', false);
});

it('shows checkout page with cart items', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();

    $this->actingAs($customer)
        ->postJson(route('store.cart.items.store'), [
            'id' => $product->slug,
            'qty' => 1,
        ])
        ->assertOk();

    $this->actingAs($customer)
        ->get(route('store.checkout.show'))
        ->assertOk()
        ->assertSee('Checkout')
        ->assertSee($product->title)
        ->assertSee('Cash on delivery');
});

it('places a cod order and clears the cart', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();

    $this->actingAs($customer)
        ->postJson(route('store.cart.items.store'), [
            'id' => $product->slug,
            'qty' => 2,
        ])
        ->assertOk();

    $response = $this->actingAs($customer)->post(route('store.checkout.store'), [
        'customer_name' => 'Test Customer',
        'customer_email' => 'checkout@example.com',
        'customer_phone' => '9800000000',
        'shipping_address_line1' => 'Thamel',
        'shipping_city' => 'Kathmandu',
        'shipping_district' => 'Kathmandu',
        'payment_method' => 'cod',
    ]);

    $order = Order::query()->where('customer_email', 'checkout@example.com')->first();

    expect($order)->not->toBeNull()
        ->and($order->user_id)->toBe($customer->id)
        ->and($order->payment_method)->toBe(PaymentMethod::Cod)
        ->and($order->status)->toBe(OrderStatus::Confirmed)
        ->and($order->payment_status)->toBe(PaymentStatus::Pending);

    $response->assertRedirect(route('store.checkout.success', $order->order_number));

    expect(CartItem::query()->where('user_id', $customer->id)->where('product_id', $product->id)->exists())
        ->toBeFalse();
});

it('redirects guest placing order to login', function () {
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();

    CartItem::create([
        'guest_token' => 'guest-token-only-123456789012345678',
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->post(route('store.checkout.store'), [
        'customer_name' => 'Guest User',
        'customer_email' => 'guest@example.com',
        'customer_phone' => '9822222222',
        'shipping_address_line1' => 'Patan',
        'shipping_city' => 'Lalitpur',
        'shipping_district' => 'Lalitpur',
        'payment_method' => 'cod',
    ])->assertRedirect(route('login'));

    expect(Order::query()->where('customer_email', 'guest@example.com')->exists())->toBeFalse();
});

it('places an online order and redirects to payment', function () {
    config(['store.payments.gateway' => 'esewa']);

    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();

    $this->actingAs($customer)
        ->postJson(route('store.cart.items.store'), [
            'id' => $product->slug,
            'qty' => 1,
        ])
        ->assertOk();

    $response = $this->actingAs($customer)->post(route('store.checkout.store'), [
        'customer_name' => 'Online Buyer',
        'customer_email' => 'online@example.com',
        'customer_phone' => '9811111111',
        'shipping_address_line1' => 'Baneshwor',
        'shipping_city' => 'Kathmandu',
        'shipping_district' => 'Kathmandu',
        'payment_method' => 'online',
    ]);

    $order = Order::query()->where('customer_email', 'online@example.com')->firstOrFail();

    expect($order->payment_method)->toBe(PaymentMethod::Online)
        ->and($order->payment_gateway)->toBe('esewa');

    $response->assertRedirect(route('store.checkout.payment', $order->order_number));

    $this->actingAs($customer)
        ->get(route('store.checkout.payment', $order->order_number))
        ->assertOk()
        ->assertSee('Redirecting to payment', false);
});

it('returns to checkout after login when checkout was intended', function () {
    $customer = User::factory()->create([
        'role' => UserRole::Customer,
        'email' => 'intended@example.com',
        'password' => bcrypt('password123'),
    ]);
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();

    CartItem::create([
        'user_id' => $customer->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->get(route('store.checkout.show'))
        ->assertRedirect(route('login'));

    $this->post(route('login'), [
        'email' => 'intended@example.com',
        'password' => 'password123',
    ])->assertRedirect(route('store.checkout.show'));
});

it('lists recent orders on account page for customer', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();

    CartItem::create([
        'user_id' => $customer->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($customer)
        ->post(route('store.checkout.store'), [
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => '9833333333',
            'shipping_address_line1' => 'Bhaktapur',
            'shipping_city' => 'Bhaktapur',
            'shipping_district' => 'Bhaktapur',
            'payment_method' => 'cod',
        ]);

    $order = Order::query()->where('user_id', $customer->id)->firstOrFail();

    $this->actingAs($customer)
        ->get(route('account'))
        ->assertOk()
        ->assertSee($order->order_number);
});
