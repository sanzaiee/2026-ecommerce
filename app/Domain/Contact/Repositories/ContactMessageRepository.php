<?php

namespace App\Domain\Contact\Repositories;

use App\Domain\Contact\DTOs\ContactMessageFilterData;
use App\Domain\Contact\Models\ContactMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactMessageRepository implements ContactMessageRepositoryInterface
{
    public function create(array $attributes): ContactMessage
    {
        return ContactMessage::create($attributes);
    }

    public function findById(int $id): ?ContactMessage
    {
        return ContactMessage::find($id);
    }

    public function paginateForAdmin(ContactMessageFilterData $filters): LengthAwarePaginator
    {
        $query = ContactMessage::query()->latest();

        if ($filters->search) {
            $search = '%'.$filters->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('phone', 'like', $search)
                    ->orWhere('subject', 'like', $search)
                    ->orWhere('message', 'like', $search);
            });
        }

        if ($filters->read === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filters->read === 'read') {
            $query->whereNotNull('read_at');
        }

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    public function markAsRead(ContactMessage $message): ContactMessage
    {
        if ($message->read_at === null) {
            $message->update(['read_at' => now()]);
        }

        return $message->fresh();
    }

    public function delete(ContactMessage $message): void
    {
        $message->delete();
    }

    public function count(): int
    {
        return ContactMessage::count();
    }

    public function countUnread(): int
    {
        return ContactMessage::whereNull('read_at')->count();
    }
}
