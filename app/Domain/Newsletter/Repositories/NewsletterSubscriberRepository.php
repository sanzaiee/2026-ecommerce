<?php

namespace App\Domain\Newsletter\Repositories;

use App\Domain\Newsletter\DTOs\NewsletterSubscriberFilterData;
use App\Domain\Newsletter\Models\NewsletterSubscriber;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NewsletterSubscriberRepository implements NewsletterSubscriberRepositoryInterface
{
    public function subscribe(string $email): NewsletterSubscriber
    {
        $email = strtolower(trim($email));
        $subscriber = NewsletterSubscriber::query()->where('email', $email)->first();

        if ($subscriber) {
            if ($subscriber->unsubscribed_at !== null) {
                $subscriber->update([
                    'unsubscribed_at' => null,
                    'subscribed_at' => now(),
                ]);
            }

            return $subscriber->fresh();
        }

        return NewsletterSubscriber::create([
            'email' => $email,
            'subscribed_at' => now(),
        ]);
    }

    public function findById(int $id): ?NewsletterSubscriber
    {
        return NewsletterSubscriber::find($id);
    }

    public function paginateForAdmin(NewsletterSubscriberFilterData $filters): LengthAwarePaginator
    {
        $query = NewsletterSubscriber::query()->latest('subscribed_at');

        if ($filters->search) {
            $query->where('email', 'like', '%'.$filters->search.'%');
        }

        match ($filters->status) {
            'unsubscribed' => $query->whereNotNull('unsubscribed_at'),
            'all' => null,
            default => $query->whereNull('unsubscribed_at'),
        };

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    public function delete(NewsletterSubscriber $subscriber): void
    {
        $subscriber->delete();
    }

    public function countActive(): int
    {
        return NewsletterSubscriber::whereNull('unsubscribed_at')->count();
    }
}
