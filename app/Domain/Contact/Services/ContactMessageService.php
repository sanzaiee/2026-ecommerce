<?php

namespace App\Domain\Contact\Services;

use App\Domain\Contact\DTOs\ContactMessageFilterData;
use App\Domain\Contact\DTOs\CreateContactMessageData;
use App\Domain\Contact\Models\ContactMessage;
use App\Domain\Contact\Repositories\ContactMessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactMessageService
{
    public function __construct(private ContactMessageRepositoryInterface $repository) {}

    public function submit(CreateContactMessageData $data): ContactMessage
    {
        return $this->repository->create($data->toArray());
    }

    public function paginateForAdmin(ContactMessageFilterData $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($filters);
    }

    public function find(int $id): ContactMessage
    {
        $message = $this->repository->findById($id);

        if (! $message) {
            throw new \RuntimeException('Contact message not found.');
        }

        return $message;
    }

    public function markAsRead(int $id): ContactMessage
    {
        return $this->repository->markAsRead($this->find($id));
    }

    public function delete(int $id): void
    {
        $this->repository->delete($this->find($id));
    }
}
