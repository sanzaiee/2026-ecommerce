<?php

namespace App\Domain\Contact\Repositories;

use App\Domain\Contact\DTOs\ContactMessageFilterData;
use App\Domain\Contact\Models\ContactMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ContactMessageRepositoryInterface
{
    public function create(array $attributes): ContactMessage;

    public function findById(int $id): ?ContactMessage;

    public function paginateForAdmin(ContactMessageFilterData $filters): LengthAwarePaginator;

    public function markAsRead(ContactMessage $message): ContactMessage;

    public function delete(ContactMessage $message): void;

    public function count(): int;

    public function countUnread(): int;
}
