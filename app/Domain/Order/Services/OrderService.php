<?php

namespace App\Domain\Order\Services;

use App\Domain\Cart\Services\CartService;
use App\Domain\Order\DTOs\CheckoutTotalsData;
use App\Domain\Order\DTOs\PlaceOrderData;
use App\Domain\Order\Models\Order;
use App\Domain\Order\Models\OrderItem;
use App\Domain\Order\Payment\PaymentGatewayManager;
use App\Domain\Stock\Services\StockManagementService;
use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Support\Store\CheckoutPaymentOptions;
use App\Support\Store\StoreOwnerContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private CartService $cart,
        private CheckoutPricingService $pricing,
        private PaymentGatewayManager $gateways,
        private CheckoutPaymentOptions $paymentOptions,
        private StockManagementService $stock,
    ) {}

    public function totalsForOwner(StoreOwnerContext $owner): CheckoutTotalsData
    {
        $snapshot = $this->cart->snapshot($owner);

        return $this->pricing->calculate(
            (float) $snapshot['subtotal'],
            (int) $snapshot['itemCount'],
        );
    }

    public function place(StoreOwnerContext $owner, PlaceOrderData $data): Order
    {
        $snapshot = $this->cart->snapshot($owner);

        if ($snapshot['itemCount'] < 1) {
            throw ValidationException::withMessages([
                'cart' => ['Your cart is empty.'],
            ]);
        }

        $totals = $this->pricing->calculate(
            (float) $snapshot['subtotal'],
            (int) $snapshot['itemCount'],
        );

        $this->assertPaymentMethodAllowed($data->paymentMethod, $totals->total);

        foreach ($snapshot['items'] as $line) {
                $product = \App\Domain\Product\Models\Product::query()
                    ->where('slug', $line['id'])
                    ->first();

                if (! $product) {
                    continue;
                }

                if (! $product->hasStockFor($line['qty'])) {
                    throw ValidationException::withMessages([
                        'cart' => ["{$line['name']} has insufficient stock. Available: {$product->stock_quantity}, Requested: {$line['qty']}."],
                    ]);
                }
            }

        return DB::transaction(function () use ($owner, $data, $snapshot, $totals) {
            $orderNumber = $this->generateOrderNumber();
            $gatewayName = $data->paymentMethod === PaymentMethod::Online
                ? $this->gateways->activeGatewayName()
                : null;

            $order = Order::query()->create([
                'order_number' => $orderNumber,
                'user_id' => $owner->isAuthenticated() ? $owner->user->id : null,
                'guest_token' => $owner->isAuthenticated() ? null : $owner->guestToken,
                'status' => OrderStatus::Pending,
                'delivery_status' => DeliveryStatus::Pending,
                'payment_method' => $data->paymentMethod,
                'payment_status' => PaymentStatus::Pending,
                'payment_gateway' => $gatewayName,
                'payment_reference' => $data->paymentMethod === PaymentMethod::Online ? $orderNumber : null,
                'subtotal' => $totals->subtotal,
                'shipping_amount' => $totals->shippingAmount,
                'total' => $totals->total,
                'currency' => config('store.currency', 'NPR'),
                'customer_name' => $data->customerName,
                'customer_email' => $data->customerEmail,
                'customer_phone' => $data->customerPhone,
                'shipping_address_line1' => $data->shippingAddressLine1,
                'shipping_address_line2' => $data->shippingAddressLine2,
                'shipping_city' => $data->shippingCity,
                'shipping_district' => $data->shippingDistrict,
                'shipping_postal_code' => $data->shippingPostalCode,
                'notes' => $data->notes,
                'placed_at' => now(),
            ]);

            foreach ($snapshot['items'] as $line) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => null,
                    'product_title' => $line['name'],
                    'product_slug' => $line['id'],
                    'unit_price' => $line['price'],
                    'quantity' => $line['qty'],
                    'line_total' => round($line['price'] * $line['qty'], 2),
                ]);
            }

            $this->attachProductIds($order);

            $this->stock->reserveStock($order);

            $this->cart->clear($owner);

            if ($owner->isAuthenticated()) {
                $owner->user->update([
                    'phone' => $data->customerPhone,
                    'shipping_address_line1' => $data->shippingAddressLine1,
                    'shipping_address_line2' => $data->shippingAddressLine2,
                    'shipping_city' => $data->shippingCity,
                    'shipping_district' => $data->shippingDistrict,
                    'shipping_postal_code' => $data->shippingPostalCode,
                ]);
            }

            if ($data->paymentMethod === PaymentMethod::Cod) {
                $order->update([
                    'status' => OrderStatus::Confirmed,
                ]);
            }

            return $order->fresh(['items']);
        });
    }

    public function markPaid(Order $order): Order
    {
        $order->update([
            'payment_status' => PaymentStatus::Paid,
            'status' => OrderStatus::Confirmed,
            'paid_at' => now(),
        ]);

        return $order->fresh();
    }

    public function markPaymentFailed(Order $order): Order
    {
        $this->stock->releaseStock($order);

        $order->update([
            'payment_status' => PaymentStatus::Failed,
            'status' => OrderStatus::Cancelled,
            'delivery_status' => DeliveryStatus::Cancelled,
        ]);

        return $order->fresh();
    }

    public function findByNumber(string $orderNumber): ?Order
    {
        return Order::query()
            ->where('order_number', $orderNumber)
            ->with('items')
            ->first();
    }

    public function canView(StoreOwnerContext $owner, Order $order): bool
    {
        if ($owner->isAuthenticated()) {
            return (int) $order->user_id === (int) $owner->user->id;
        }

        return $order->guest_token !== null
            && $order->guest_token === $owner->guestToken;
    }

    private function assertPaymentMethodAllowed(PaymentMethod $method, float $total): void
    {
        if ($method === PaymentMethod::Cod && ! $this->paymentOptions->isCodAvailable($total)) {
            throw ValidationException::withMessages([
                'payment_method' => ['Cash on delivery is not available for this order.'],
            ]);
        }

        if ($method === PaymentMethod::Online && ! $this->gateways->isOnlineEnabled()) {
            throw ValidationException::withMessages([
                'payment_method' => ['Online payment is not available right now.'],
            ]);
        }
    }

    private function attachProductIds(Order $order): void
    {
        foreach ($order->items as $item) {
            $productId = \App\Domain\Product\Models\Product::query()
                ->where('slug', $item->product_slug)
                ->value('id');

            if ($productId) {
                $item->update(['product_id' => $productId]);
            }
        }
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'MF-'.now()->format('ymd').'-'.strtoupper(Str::random(6));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
