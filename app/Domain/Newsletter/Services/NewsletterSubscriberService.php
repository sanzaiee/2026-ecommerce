<?php

namespace App\Domain\Newsletter\Services;

use App\Domain\Newsletter\DTOs\NewsletterSubscriberFilterData;
use App\Domain\Newsletter\Models\NewsletterSubscriber;
use App\Domain\Newsletter\Repositories\NewsletterSubscriberRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NewsletterSubscriberService
{
    public function __construct(private NewsletterSubscriberRepositoryInterface $repository) {}

    public function subscribe(string $email): NewsletterSubscriber
    {
        return $this->repository->subscribe($email);
    }

    public function paginateForAdmin(NewsletterSubscriberFilterData $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($filters);
    }

    public function delete(int $id): void
    {
        $subscriber = $this->repository->findById($id);

        if (! $subscriber) {
            throw new \RuntimeException('Subscriber not found.');
        }

        $this->repository->delete($subscriber);
    }
}
