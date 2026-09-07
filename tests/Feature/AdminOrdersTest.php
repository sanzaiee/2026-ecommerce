<?php

use App\Domain\Order\Models\Order;
use App\Domain\Product\Models\Product;
use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(CatalogSeeder::class);
});

function placeTestOrder(User $customer): Order
{
    $product = Product::query()->where('slug', 'premium-dried-mango-slices')->firstOrFail();
    $product->update(['stock_quantity' => max(25, (int) $product->stock_quantity)]);

    test()->actingAs($customer)
        ->postJson(route('store.cart.items.store'), [
            'id' => $product->slug,
            'qty' => 1,
        ])
        ->assertOk();

    test()->actingAs($customer)->post(route('store.checkout.store'), [
        'customer_name' => 'Admin Test Customer',
        'customer_email' => 'admin-order@example.com',
        'customer_phone' => '9800000001',
        'shipping_address_line1' => 'Thamel',
        'shipping_city' => 'Kathmandu',
        'shipping_district' => 'Kathmandu',
        'payment_method' => 'cod',
    ]);

    return Order::query()->where('customer_email', 'admin-order@example.com')->firstOrFail();
}

it('allows admin to list orders', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $order = placeTestOrder($customer);

    $this->actingAs($admin)
        ->get(route('admin.orders.index'))
        ->assertOk()
        ->assertSee($order->order_number)
        ->assertSee('Admin Test Customer');
});

it('forbids customer from admin orders', function () {
    $customer = User::factory()->create(['role' => UserRole::Customer]);

    $this->actingAs($customer)
        ->get(route('admin.orders.index'))
        ->assertRedirect(route('admin.login'));
});

it('shows order detail for admin', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $order = placeTestOrder($customer);

    $this->actingAs($admin)
        ->get(route('admin.orders.show', $order))
        ->assertOk()
        ->assertSee($order->order_number)
        ->assertSee('Premium Dried Mango Slices');
});

it('shows printable invoice for admin', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $order = placeTestOrder($customer);

    $this->actingAs($admin)
        ->get(route('admin.orders.invoice', $order))
        ->assertOk()
        ->assertSee('Invoice')
        ->assertSee($order->order_number);
});

it('applies storefront theme colors on the invoice', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $order = placeTestOrder($customer);

    $this->actingAs($admin)
        ->put(route('admin.settings.update'), [
            'site_name' => 'Test Store',
            'theme_primary' => '#AABBCC',
            'theme_primary_dark' => '#112233',
        ])
        ->assertRedirect();

    $this->actingAs($admin)
        ->get(route('admin.orders.invoice', $order))
        ->assertOk()
        ->assertSee('--primary: #AABBCC', false)
        ->assertSee('--primary-dark: #112233', false);
});

it('updates delivery status for admin', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
        'password' => Hash::make('password'),
    ]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $order = placeTestOrder($customer);

    $this->actingAs($admin)
        ->patch(route('admin.orders.delivery-status', $order), [
            'delivery_status' => DeliveryStatus::Shipped->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('status');

    $order->refresh();

    expect($order->delivery_status)->toBe(DeliveryStatus::Shipped)
        ->and($order->shipped_at)->not->toBeNull();
});

it('updates order status for admin', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $order = placeTestOrder($customer);

    $this->actingAs($admin)
        ->patch(route('admin.orders.status', $order), [
            'status' => OrderStatus::Cancelled->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('status');

    $order->refresh();

    expect($order->status)->toBe(OrderStatus::Cancelled)
        ->and($order->delivery_status)->toBe(DeliveryStatus::Cancelled);
});

it('marks order as paid for admin', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $customer = User::factory()->create(['role' => UserRole::Customer]);
    $order = placeTestOrder($customer);

    $this->actingAs($admin)
        ->patch(route('admin.orders.mark-paid', $order))
        ->assertRedirect()
        ->assertSessionHas('status');

    $order->refresh();

    expect($order->payment_status)->toBe(PaymentStatus::Paid)
        ->and($order->paid_at)->not->toBeNull();
});
