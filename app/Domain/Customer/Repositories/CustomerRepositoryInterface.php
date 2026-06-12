<?php

namespace App\Domain\Customer\Repositories;

use App\Domain\Customer\DTOs\CustomerFilterData;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CustomerRepositoryInterface
{
    public function findById(int $id): ?User;

    public function paginateForAdmin(CustomerFilterData $filters): LengthAwarePaginator;

    public function ban(User $customer): User;

    public function unban(User $customer): User;

    public function count(): int;

    public function countBanned(): int;

    /**
     * @return Collection<int, User>
     */
    public function recentCustomers(int $limit = 5): Collection;
}