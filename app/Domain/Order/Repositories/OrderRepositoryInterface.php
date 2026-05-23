<?php

namespace App\Domain\Order\Repositories;

use App\Domain\Order\DTOs\OrderFilterData;
use App\Domain\Order\Models\Order;
use App\Enums\OrderStatus;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function paginateForAdmin(OrderFilterData $filters): LengthAwarePaginator;

    public function findByIdForAdmin(int $id): ?Order;

    public function countAll(): int;

    public function countSince(CarbonInterface $since): int;

    public function countByStatus(OrderStatus $status): int;

    public function countAwaitingDelivery(): int;

    /**
     * @return array<string, int>
     */
    public function countGroupedByOrderStatus(): array;

    /**
     * @return array<string, int>
     */
    public function countGroupedByPaymentStatus(): array;

    public function sumPaidRevenue(?CarbonInterface $since = null): float;

    /**
     * @return list<array{date: string, revenue: float, orders: int}>
     */
    public function paidRevenueByDay(int $days = 30): array;

    /**
     * @return Collection<int, Order>
     */
    public function recentForAdmin(int $limit = 8): Collection;

    /**
     * @return list<array{product_id: int, product_title: string, units_sold: int, revenue: float}>
     */
    public function topSellingProducts(int $limit = 5): array;
}
