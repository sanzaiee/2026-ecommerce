<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Order\DTOs\OrderFilterData;
use App\Domain\Order\Models\Order;
use App\Domain\Order\Services\OrderAdminService;
use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateDeliveryStatusRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private OrderAdminService $orders) {}

    public function index(Request $request): View
    {
        $filters = new OrderFilterData(
            search: $request->string('search')->toString() ?: null,
            status: $request->enum('status', OrderStatus::class),
            deliveryStatus: $request->enum('delivery_status', DeliveryStatus::class),
            paymentStatus: $request->enum('payment_status', PaymentStatus::class),
            perPage: 20,
            page: max(1, (int) $request->input('page', 1)),
        );

        return view('admin.orders.index', [
            'orders' => $this->orders->paginate($filters),
            'filters' => $filters,
        ]);
    }

    public function show(Order $order): View
    {
        $order = $this->orders->find($order->id);

        return view('admin.orders.show', compact('order'));
    }

    public function invoice(Order $order): View
    {
        $order = $this->orders->find($order->id);

        return view('admin.orders.invoice', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->orders->updateOrderStatus(
            $order,
            OrderStatus::from($request->validated('status')),
        );

        return back()->with('status', 'Order status updated.');
    }

    public function updateDeliveryStatus(UpdateDeliveryStatusRequest $request, Order $order): RedirectResponse
    {
        $this->orders->updateDeliveryStatus(
            $order,
            DeliveryStatus::from($request->validated('delivery_status')),
        );

        return back()->with('status', 'Delivery status updated.');
    }

    public function markPaid(Order $order): RedirectResponse
    {
        $this->orders->markPaid($order);

        return back()->with('status', 'Payment marked as paid.');
    }
}
