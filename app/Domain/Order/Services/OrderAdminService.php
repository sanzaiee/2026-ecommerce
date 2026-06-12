<?php

namespace App\Domain\Order\Services;

use App\Domain\Order\DTOs\OrderFilterData;
use App\Domain\Order\Models\Order;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Stock\Services\StockManagementService;
use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderAdminService
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private StockManagementService $stock,
    ) {}

    public function paginate(OrderFilterData $filters): LengthAwarePaginator
    {
        return $this->orders->paginateForAdmin($filters);
    }

    public function find(int $id): Order
    {
        $order = $this->orders->findByIdForAdmin($id);

        if (! $order) {
            throw new NotFoundHttpException('Order not found.');
        }

        return $order;
    }

    public function updateOrderStatus(Order $order, OrderStatus $status): Order
    {
        $oldStatus = $order->status;

        $attributes = ['status' => $status];

        if ($status === OrderStatus::Cancelled) {
            $attributes['delivery_status'] = DeliveryStatus::Cancelled;
        }

        $order->update($attributes);

        if ($oldStatus !== OrderStatus::Cancelled && $status === OrderStatus::Cancelled) {
            $this->stock->releaseStock($order);
        }

        return $order->fresh(['items', 'user']);
    }

    public function updateDeliveryStatus(Order $order, DeliveryStatus $status): Order
    {
        $oldStatus = $order->delivery_status;

        $attributes = ['delivery_status' => $status];

        if ($status === DeliveryStatus::Shipped && $order->shipped_at === null) {
            $attributes['shipped_at'] = now();
        }

        if ($status === DeliveryStatus::Delivered && $order->delivered_at === null) {
            $attributes['delivered_at'] = now();
        }

        if ($status === DeliveryStatus::Cancelled) {
            $attributes['status'] = OrderStatus::Cancelled;
        }

        $order->update($attributes);

        if ($oldStatus !== DeliveryStatus::Cancelled && $status === DeliveryStatus::Cancelled) {
            $this->stock->releaseStock($order);
        }

        return $order->fresh(['items', 'user']);
    }

    public function markPaid(Order $order): Order
    {
        $order->update([
            'payment_status' => PaymentStatus::Paid,
            'status' => OrderStatus::Confirmed,
            'paid_at' => $order->paid_at ?? now(),
        ]);

        return $order->fresh(['items', 'user']);
    }
}
