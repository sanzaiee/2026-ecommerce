<?php

namespace App\Http\Controllers\Store;

use App\Domain\Cart\Services\CartService;
use App\Domain\Order\Payment\PaymentGatewayManager;
use App\Domain\Order\Services\OrderService;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\PlaceOrderRequest;
use App\Support\Store\CheckoutPaymentOptions;
use App\Support\Store\StoreOwnerResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderService $orders,
        private CartService $cart,
        private PaymentGatewayManager $gateways,
        private CheckoutPaymentOptions $paymentOptions,
        private StoreOwnerResolver $ownerResolver,
    ) {}

    public function show(Request $request): View|RedirectResponse
    {
        $owner = $this->ownerResolver->fromRequest($request);
        $snapshot = $this->cart->snapshot($owner);

        if (($snapshot['itemCount'] ?? 0) < 1) {
            return redirect()
                ->route('shop')
                ->with('status', 'Your cart is empty. Add items before checkout.');
        }

        $totals = $this->orders->totalsForOwner($owner);
        $user = $request->user();

        if (! $this->paymentOptions->hasAny($totals->total)) {
            return redirect()
                ->route('shop')
                ->with('status', 'Checkout is unavailable. Please contact us to place your order.');
        }

        return view('checkout.index', [
            'cartTotal' => 'Rs. '.number_format($totals->total, 0, '.', ','),
            'items' => $snapshot['items'],
            'totals' => $totals,
            'paymentOptions' => $this->paymentOptions->available($totals->total),
            'defaultPaymentMethod' => $this->paymentOptions->defaultMethod($totals->total),
            'freeShippingThreshold' => (float) config('store.shipping.free_threshold', 2000),
            'customer' => [
                'name' => old('customer_name', $user?->name),
                'email' => old('customer_email', $user?->email),
                'phone' => old('customer_phone', $user?->phone),
            ],
            'shipping' => [
                'address_line1' => old('shipping_address_line1', $user?->shipping_address_line1),
                'address_line2' => old('shipping_address_line2', $user?->shipping_address_line2),
                'city' => old('shipping_city', $user?->shipping_city),
                'district' => old('shipping_district', $user?->shipping_district),
                'postal_code' => old('shipping_postal_code', $user?->shipping_postal_code),
            ],
        ]);
    }

    public function store(PlaceOrderRequest $request): RedirectResponse
    {
        $owner = $this->ownerResolver->fromRequest($request);
        $order = $this->orders->place($owner, $request->toDto());

        $request->session()->put('store.last_order_number', $order->order_number);

        if ($order->payment_method === PaymentMethod::Online) {
            $response = redirect()->route('store.checkout.payment', $order->order_number);
        } else {
            $response = redirect()
                ->route('store.checkout.success', $order->order_number)
                ->with('status', 'Thank you! Your order has been placed.');
        }

        return $response;
    }

    public function payment(Request $request, string $orderNumber): View|RedirectResponse
    {
        $order = $this->orders->findByNumber($orderNumber);

        if (! $order || $order->payment_method !== PaymentMethod::Online || $order->isPaid()) {
            abort(404);
        }

        $owner = $this->ownerResolver->fromRequest($request);

        if (! $this->orders->canView($owner, $order)) {
            abort(403);
        }

        $gateway = $this->gateways->resolve();
        $initiation = $gateway->initiate($order);

        return view('checkout.payment-redirect', [
            'order' => $order,
            'formAction' => $initiation->formAction,
            'fields' => $initiation->fields,
            'cartTotal' => 'Rs. '.number_format((float) $order->total, 0, '.', ','),
        ]);
    }

    public function paymentSuccess(Request $request, string $orderNumber): RedirectResponse
    {
        $order = $this->orders->findByNumber($orderNumber);

        if (! $order) {
            abort(404);
        }

        $owner = $this->ownerResolver->fromRequest($request);

        if (! $this->orders->canView($owner, $order)) {
            abort(403);
        }

        if ($order->isPaid()) {
            return redirect()->route('store.checkout.success', $order->order_number);
        }

        $gateway = $this->gateways->resolve();

        if ($gateway->verifyReturn($request, $order)) {
            $this->orders->markPaid($order);

            return redirect()
                ->route('store.checkout.success', $order->order_number)
                ->with('status', 'Payment received. Thank you for your order!');
        }

        $this->orders->markPaymentFailed($order);

        return redirect()
            ->route('store.checkout.failure', $order->order_number)
            ->with('status', 'Payment could not be verified. Please try again or choose another method.');
    }

    public function paymentFailure(Request $request, string $orderNumber): RedirectResponse
    {
        $order = $this->orders->findByNumber($orderNumber);

        if (! $order) {
            abort(404);
        }

        $owner = $this->ownerResolver->fromRequest($request);

        if (! $this->orders->canView($owner, $order)) {
            abort(403);
        }

        if (! $order->isPaid()) {
            $this->orders->markPaymentFailed($order);
        }

        return redirect()
            ->route('store.checkout.failure', $order->order_number)
            ->with('status', 'Payment was cancelled or failed.');
    }

    public function success(Request $request, string $orderNumber): View
    {
        $order = $this->orders->findByNumber($orderNumber);

        if (! $order) {
            abort(404);
        }

        $owner = $this->ownerResolver->fromRequest($request);

        if (! $this->orders->canView($owner, $order)) {
            abort(403);
        }

        return view('checkout.success', [
            'order' => $order,
            'cartTotal' => 'Rs. 0',
        ]);
    }

    public function failure(Request $request, string $orderNumber): View
    {
        $order = $this->orders->findByNumber($orderNumber);

        if (! $order) {
            abort(404);
        }

        $owner = $this->ownerResolver->fromRequest($request);

        if (! $this->orders->canView($owner, $order)) {
            abort(403);
        }

        return view('checkout.failure', [
            'order' => $order,
            'cartTotal' => 'Rs. 0',
        ]);
    }
}
