<?php

namespace App\Domain\Customer\Repositories;

use App\Domain\Customer\DTOs\CustomerFilterData;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::with(['orders', 'reviews'])
            ->where('role', UserRole::Customer)
            ->find($id);
    }

    public function paginateForAdmin(CustomerFilterData $filters): LengthAwarePaginator
    {
        $query = User::where('role', UserRole::Customer)
            ->withCount(['orders', 'reviews'])
            ->with(['orders' => function ($query) {
                $query->latest()->limit(3);
            }])
            ->latest();

        if ($filters->search) {
            $query->where(function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters->search . '%')
                    ->orWhere('email', 'like', '%' . $filters->search . '%')
                    ->orWhere('phone', 'like', '%' . $filters->search . '%');
            });
        }

        if ($filters->isBanned !== null) {
            $query->where('is_banned', $filters->isBanned);
        }

        if ($filters->hasOrders !== null) {
            if ($filters->hasOrders) {
                $query->whereHas('orders');
            } else {
                $query->whereDoesntHave('orders');
            }
        }

        if ($filters->orderBy) {
            $direction = $filters->orderDirection ?? 'desc';
            switch ($filters->orderBy) {
                case 'orders_count':
                    $query->orderBy('orders_count', $direction);
                    break;
                case 'total_spent':
                    // This will be handled by the service with a custom query
                    break;
                default:
                    $query->orderBy($filters->orderBy, $direction);
            }
        }

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    public function ban(User $customer): User
    {
        $customer->update(['is_banned' => true, 'banned_at' => now()]);

        return $customer->fresh();
    }

    public function unban(User $customer): User
    {
        $customer->update(['is_banned' => false, 'banned_at' => null]);

        return $customer->fresh();
    }

    public function count(): int
    {
        return User::where('role', UserRole::Customer)->count();
    }

    public function countBanned(): int
    {
        return User::where('role', UserRole::Customer)
            ->where('is_banned', true)
            ->count();
    }

    public function recentCustomers(int $limit = 5): Collection
    {
        return User::where('role', UserRole::Customer)
            ->latest()
            ->limit($limit)
            ->get();
    }
}