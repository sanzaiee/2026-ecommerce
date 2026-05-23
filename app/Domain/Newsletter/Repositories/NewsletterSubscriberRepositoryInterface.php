<?php

namespace App\Domain\Newsletter\Repositories;

use App\Domain\Newsletter\DTOs\NewsletterSubscriberFilterData;
use App\Domain\Newsletter\Models\NewsletterSubscriber;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NewsletterSubscriberRepositoryInterface
{
    public function subscribe(string $email): NewsletterSubscriber;

    public function findById(int $id): ?NewsletterSubscriber;

    public function paginateForAdmin(NewsletterSubscriberFilterData $filters): LengthAwarePaginator;

    public function delete(NewsletterSubscriber $subscriber): void;

    public function countActive(): int;
}
