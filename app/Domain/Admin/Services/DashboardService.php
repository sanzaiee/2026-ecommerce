<?php

namespace App\Domain\Admin\Services;

use App\Domain\Brand\Repositories\BrandRepositoryInterface;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Contact\Repositories\ContactMessageRepositoryInterface;
use App\Domain\Newsletter\Repositories\NewsletterSubscriberRepositoryInterface;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Review\Repositories\ReviewRepositoryInterface;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\User;

class DashboardService
{
    public function __construct(
        private ProductRepositoryInterface $products,
        private CategoryRepositoryInterface $categories,
        private BrandRepositoryInterface $brands,
        private ReviewRepositoryInterface $reviews,
        private ContactMessageRepositoryInterface $contactMessages,
        private NewsletterSubscriberRepositoryInterface $newsletterSubscribers,
        private OrderRepositoryInterface $orders,
    ) {}

    /**
     * @return array<string, int>
     */
    public function stats(): array
    {
        return [
            'total_products' => $this->products->count(),
            'total_categories' => $this->categories->count(),
            'total_brands' => $this->brands->count(),
            'total_reviews' => $this->reviews->count(),
            'pending_reviews' => $this->reviews->countPending(),
            'unread_contact_messages' => $this->contactMessages->countUnread(),
            'newsletter_subscribers' => $this->newsletterSubscribers->countActive(),
            'out_of_stock_products' => $this->products->countOutOfStock(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function analytics(): array
    {
        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $pendingReviews = $this->reviews->countPending();
        $unreadMessages = $this->contactMessages->countUnread();
        $outOfStock = $this->products->countOutOfStock();
        $pendingOrders = $this->orders->countByStatus(OrderStatus::Pending);

        return [
            'orders' => [
                'total' => $this->orders->countAll(),
                'today' => $this->orders->countSince($today),
                'this_month' => $this->orders->countSince($monthStart),
                'pending' => $pendingOrders,
                'awaiting_delivery' => $this->orders->countAwaitingDelivery(),
                'by_status' => $this->orders->countGroupedByOrderStatus(),
                'by_payment' => $this->orders->countGroupedByPaymentStatus(),
            ],
            'revenue' => [
                'total' => $this->orders->sumPaidRevenue(),
                'this_month' => $this->orders->sumPaidRevenue($monthStart),
                'today' => $this->orders->sumPaidRevenue($today),
                'daily' => $this->orders->paidRevenueByDay(30),
            ],
            'customers' => User::query()->where('role', UserRole::Customer)->count(),
            'recent_orders' => $this->orders->recentForAdmin(8),
            'top_products' => $this->orders->topSellingProducts(5),
            'attention' => [
                'pending_reviews' => $pendingReviews,
                'unread_messages' => $unreadMessages,
                'out_of_stock' => $outOfStock,
                'pending_orders' => $pendingOrders,
                'total' => $pendingReviews + $unreadMessages + $outOfStock + $pendingOrders,
            ],
        ];
    }
}
