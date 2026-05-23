<?php

use App\Domain\Cart\Models\CartItem;
use App\Domain\Order\Models\Order;
use App\Domain\Product\Models\Product;
use App\Enums\StockStatus;
use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

it('requires authentication for account sections', function () {
    $this->get(route('account.orders'))->assertRedirect(route('login'));
    $this->get(route('account.profile'))->assertRedirect(route('login'));
});

it('redirects admins away from account sections', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('account'))
        ->assertRedirect(route('admin.dashboard'));
});

it('shows account dashboard for customer', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $this->actingAs($customer)
        ->get(route('account'))
        ->assertOk()
        ->assertSee('Welcome back')
        ->assertSee($customer->email);
});

it('lists orders on account orders page', function () {
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
        ->get(route('account.orders'))
        ->assertOk()
        ->assertSee('Order history')
        ->assertSee($order->order_number);
});

it('shows order detail for owning customer', function () {
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
        ->get(route('account.orders.show', $order->order_number))
        ->assertOk()
        ->assertSee($order->order_number)
        ->assertSee($product->title);
});

it('forbids viewing another customers order', function () {
    $owner = User::factory()->create(['role' => UserRole::Customer]);
    $other = User::factory()->create(['role' => UserRole::Customer]);
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();

    CartItem::create([
        'user_id' => $owner->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($owner)
        ->post(route('store.checkout.store'), [
            'customer_name' => $owner->name,
            'customer_email' => $owner->email,
            'customer_phone' => '9833333333',
            'shipping_address_line1' => 'Bhaktapur',
            'shipping_city' => 'Bhaktapur',
            'shipping_district' => 'Bhaktapur',
            'payment_method' => 'cod',
        ]);

    $order = Order::query()->where('user_id', $owner->id)->firstOrFail();

    $this->actingAs($other)
        ->get(route('account.orders.show', $order->order_number))
        ->assertNotFound();
});

it('updates customer profile name', function () {
    $customer = User::factory()->create([
        'role' => UserRole::Customer,
        'name' => 'Old Name',
    ]);

    $this->actingAs($customer)
        ->put(route('account.profile.update'), ['name' => 'New Name'])
        ->assertRedirect(route('account.profile'))
        ->assertSessionHas('status');

    expect($customer->fresh()->name)->toBe('New Name');
});

it('shows account wishlist page for customer', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();

    \App\Domain\Wishlist\Models\WishlistItem::create([
        'user_id' => $customer->id,
        'product_id' => $product->id,
    ]);

    $this->actingAs($customer)
        ->get(route('account.wishlist'))
        ->assertOk()
        ->assertSee('My wishlist')
        ->assertSee($product->title);
});

it('paginates account wishlist items', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $baseProduct = Product::query()->firstOrFail();

    $extraProduct = Product::query()->updateOrCreate(
        ['slug' => 'pagination-test-product'],
        [
            'title' => 'Pagination Test Product',
            'price' => 199,
            'stock_status' => StockStatus::InStock,
            'category_id' => $baseProduct->category_id,
            'brand_id' => $baseProduct->brand_id,
        ],
    );

    $products = Product::query()->orderBy('id')->get();

    foreach ($products as $product) {
        \App\Domain\Wishlist\Models\WishlistItem::create([
            'user_id' => $customer->id,
            'product_id' => $product->id,
        ]);
    }

    $total = $products->count();

    $this->actingAs($customer)
        ->get(route('account.wishlist'))
        ->assertOk()
        ->assertSee("{$total} items saved")
        ->assertSee('Showing 1&ndash;12', false);

    $oldestWishlistProduct = $products->first();
    $newestWishlistProduct = $products->last();

    $this->actingAs($customer)
        ->get(route('account.wishlist', ['page' => 2]))
        ->assertOk()
        ->assertSee("{$total} items saved")
        ->assertSee('Showing 13&ndash;'.$total, false)
        ->assertSee($oldestWishlistProduct->title)
        ->assertDontSee($newestWishlistProduct->title);
});

it('redirects store wishlist url to account for logged-in customer', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $this->actingAs($customer)
        ->get(route('store.wishlist.show'))
        ->assertRedirect(route('account.wishlist'));
});

it('shows guest wishlist page html', function () {
    $this->get(route('store.wishlist.show'))
        ->assertOk()
        ->assertSee('Your wishlist');
});

it('returns wishlist json for api requests', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $this->actingAs($customer)
        ->getJson(route('store.wishlist.show'))
        ->assertOk()
        ->assertJsonStructure(['items', 'count']);
});

it('shows and updates saved delivery address', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $this->actingAs($customer)
        ->get(route('account.addresses'))
        ->assertOk()
        ->assertSee('Delivery address')
        ->assertSee('Street address');

    $this->actingAs($customer)
        ->put(route('account.addresses.update'), [
            'phone' => '9812345678',
            'shipping_address_line1' => 'Thamel, Kathmandu',
            'shipping_address_line2' => 'Suite 2',
            'shipping_city' => 'Kathmandu',
            'shipping_district' => 'Kathmandu',
            'shipping_postal_code' => '44600',
        ])
        ->assertRedirect(route('account.addresses'))
        ->assertSessionHas('status');

    $customer->refresh();

    expect($customer->phone)->toBe('9812345678')
        ->and($customer->shipping_address_line1)->toBe('Thamel, Kathmandu')
        ->and($customer->shipping_city)->toBe('Kathmandu');
});

it('updates customer password with current password', function () {
    $customer = User::factory()->create([
        'role' => UserRole::Customer,
        'password' => Hash::make('old-password'),
    ]);

    $this->actingAs($customer)
        ->put(route('account.password.update'), [
            'current_password' => 'old-password',
            'password' => 'new-password-12',
            'password_confirmation' => 'new-password-12',
        ])
        ->assertRedirect(route('account.profile'))
        ->assertSessionHas('status');

    expect(Hash::check('new-password-12', $customer->fresh()->password))->toBeTrue();
});
