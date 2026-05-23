<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Contact\DTOs\ContactMessageFilterData;
use App\Domain\Contact\Models\ContactMessage;
use App\Domain\Contact\Services\ContactMessageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function __construct(private ContactMessageService $messages) {}

    public function index(Request $request): View
    {
        $filters = new ContactMessageFilterData(
            search: $request->string('search')->toString() ?: null,
            read: $request->string('read')->toString() ?: null,
            page: max(1, (int) $request->input('page', 1)),
        );

        return view('admin.contact-messages.index', [
            'messages' => $this->messages->paginateForAdmin($filters),
            'filters' => $filters,
        ]);
    }

    public function show(ContactMessage $contactMessage): View
    {
        if ($contactMessage->isUnread()) {
            $contactMessage = $this->messages->markAsRead($contactMessage->id);
        }

        return view('admin.contact-messages.show', [
            'message' => $contactMessage,
        ]);
    }

    public function markAsRead(ContactMessage $contactMessage): RedirectResponse
    {
        $this->messages->markAsRead($contactMessage->id);

        return back()->with('status', 'Message marked as read.');
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $this->messages->delete($contactMessage->id);

        return redirect()
            ->route('admin.contact-messages.index')
            ->with('status', 'Message deleted.');
    }
}
