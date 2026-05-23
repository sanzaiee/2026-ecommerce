<?php

namespace App\Domain\Order\Repositories;

use App\Domain\Order\DTOs\OrderFilterData;
use App\Domain\Order\Models\Order;
use App\Domain\Order\Models\OrderItem;
use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function paginateForAdmin(OrderFilterData $filters): LengthAwarePaginator
    {
        $query = Order::query()->with('user')->latest('placed_at');

        if ($filters->search) {
            $search = '%'.$filters->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', $search)
                    ->orWhere('customer_name', 'like', $search)
                    ->orWhere('customer_email', 'like', $search)
                    ->orWhere('customer_phone', 'like', $search);
            });
        }

        if ($filters->status) {
            $query->where('status', $filters->status);
        }

        if ($filters->deliveryStatus) {
            $query->where('delivery_status', $filters->deliveryStatus);
        }

        if ($filters->paymentStatus) {
            $query->where('payment_status', $filters->paymentStatus);
        }

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    public function findByIdForAdmin(int $id): ?Order
    {
        return Order::query()
            ->with(['items', 'user'])
            ->find($id);
    }

    public function countAll(): int
    {
        return Order::query()->count();
    }

    public function countSince(CarbonInterface $since): int
    {
        return Order::query()
            ->where('placed_at', '>=', $since)
            ->count();
    }

    public function countByStatus(OrderStatus $status): int
    {
        return Order::query()
            ->where('status', $status)
            ->count();
    }

    public function countAwaitingDelivery(): int
    {
        return Order::query()
            ->where('status', '!=', OrderStatus::Cancelled)
            ->whereNotIn('delivery_status', [
                DeliveryStatus::Delivered,
                DeliveryStatus::Cancelled,
            ])
            ->count();
    }

    public function countGroupedByOrderStatus(): array
    {
        return Order::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->mapWithKeys(fn ($count, $status) => [(string) $status => (int) $count])
            ->all();
    }

    public function countGroupedByPaymentStatus(): array
    {
        return Order::query()
            ->select('payment_status', DB::raw('COUNT(*) as total'))
            ->groupBy('payment_status')
            ->pluck('total', 'payment_status')
            ->mapWithKeys(fn ($count, $status) => [(string) $status => (int) $count])
            ->all();
    }

    public function sumPaidRevenue(?CarbonInterface $since = null): float
    {
        $query = Order::query()
            ->where('payment_status', PaymentStatus::Paid);

        if ($since !== null) {
            $query->where('placed_at', '>=', $since);
        }

        return (float) $query->sum('total');
    }

    public function paidRevenueByDay(int $days = 30): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = Order::query()
            ->where('payment_status', PaymentStatus::Paid)
            ->where('placed_at', '>=', $start)
            ->selectRaw('DATE(placed_at) as day')
            ->selectRaw('SUM(total) as revenue')
            ->selectRaw('COUNT(*) as orders')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy(fn ($row) => (string) $row->day);

        $series = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $row = $rows->get($date);

            $series[] = [
                'date' => $date,
                'revenue' => $row ? (float) $row->revenue : 0.0,
                'orders' => $row ? (int) $row->orders : 0,
            ];
        }

        return $series;
    }

    public function recentForAdmin(int $limit = 8): Collection
    {
        return Order::query()
            ->latest('placed_at')
            ->limit($limit)
            ->get();
    }

    public function topSellingProducts(int $limit = 5): array
    {
        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', PaymentStatus::Paid)
            ->whereNotNull('order_items.product_id')
            ->select('order_items.product_id', 'order_items.product_title')
            ->selectRaw('SUM(order_items.quantity) as units_sold')
            ->selectRaw('SUM(order_items.line_total) as revenue')
            ->groupBy('order_items.product_id', 'order_items.product_title')
            ->orderByDesc('units_sold')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'product_id' => (int) $row->product_id,
                'product_title' => (string) $row->product_title,
                'units_sold' => (int) $row->units_sold,
                'revenue' => (float) $row->revenue,
            ])
            ->all();
    }
}
