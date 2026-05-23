<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Newsletter\DTOs\NewsletterSubscriberFilterData;
use App\Domain\Newsletter\Models\NewsletterSubscriber;
use App\Domain\Newsletter\Services\NewsletterSubscriberService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterSubscriberController extends Controller
{
    public function __construct(private NewsletterSubscriberService $subscribers) {}

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $filters = new NewsletterSubscriberFilterData(
            search: $request->string('search')->toString() ?: null,
            status: in_array($status, ['active', 'unsubscribed', 'all'], true) ? $status : 'active',
            page: max(1, (int) $request->input('page', 1)),
        );

        return view('admin.newsletter-subscribers.index', [
            'subscribers' => $this->subscribers->paginateForAdmin($filters),
            'filters' => $filters,
        ]);
    }

    public function destroy(NewsletterSubscriber $newsletterSubscriber): RedirectResponse
    {
        $this->subscribers->delete($newsletterSubscriber->id);

        return back()->with('status', 'Subscriber removed.');
    }
}
