<?php

namespace App\Domain\Customer\Services;

use App\Domain\Customer\DTOs\CustomerFilterData;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerAdminService
{
    public function __construct(private CustomerRepositoryInterface $customers) {}

    public function paginate(CustomerFilterData $filters): LengthAwarePaginator
    {
        return $this->customers->paginateForAdmin($filters);
    }

    public function find(int $id): User
    {
        $customer = $this->customers->findById($id);

        if (! $customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        // Additional data loading
        $customer->load([
            'orders' => function ($query) {
                $query->latest()->with('items');
            },
            'reviews' => function ($query) {
                $query->latest()->with('product');
            },
        ]);

        return $customer;
    }

    public function ban(User $customer, ?string $reason = null): User
    {
        $customer->update([
            'is_banned' => true,
            'banned_at' => now(),
            'ban_reason' => $reason,
        ]);

        return $customer->fresh();
    }

    public function unban(User $customer): User
    {
        $customer->update([
            'is_banned' => false,
            'banned_at' => null,
            'ban_reason' => null,
        ]);

        return $customer->fresh();
    }

    public function count(): int
    {
        return $this->customers->count();
    }

    public function countBanned(): int
    {
        return $this->customers->countBanned();
    }

    /**
     * @return Collection<int, User>
     */
    public function recentCustomers(int $limit = 5): Collection
    {
        return $this->customers->recentCustomers($limit);
    }
}