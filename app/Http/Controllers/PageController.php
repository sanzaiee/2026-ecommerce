<?php

namespace App\Http\Controllers;

use App\Domain\CMS\Services\AboutPageService;
use App\Domain\Contact\DTOs\CreateContactMessageData;
use App\Domain\Contact\Services\ContactMessageService;
use App\Domain\Newsletter\Services\NewsletterSubscriberService;
use App\Domain\Settings\Services\SettingsService;
use App\Support\ViewData\AboutPageMapper;
use App\Http\Requests\StoreContactMessageRequest;
use App\Http\Requests\SubscribeNewsletterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private ContactMessageService $contactMessages,
        private NewsletterSubscriberService $newsletter,
        private SettingsService $settings,
        private AboutPageService $aboutPage,
        private AboutPageMapper $aboutMapper,
    ) {}

    public function contact(): View
    {
        $site = $this->settings->getPublic();

        return view('pages.contact', [
            'cartTotal' => 'Rs. 0',
            'pageTitle' => 'Contact Us',
            'contactInfo' => $site['contactInfo'],
        ]);
    }

    public function submitContact(StoreContactMessageRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->contactMessages->submit(new CreateContactMessageData(
            name: $validated['name'],
            email: $validated['email'],
            phone: $validated['phone'] ?? null,
            subject: $validated['subject'],
            message: $validated['message'],
        ));

        return redirect()
            ->route('contact')
            ->with('status', 'Thank you! We received your message and will reply within one business day.');
    }

    public function subscribeNewsletter(SubscribeNewsletterRequest $request): RedirectResponse
    {
        $this->newsletter->subscribe($request->validated('email'));

        return back()->with('newsletter_status', 'You are subscribed! Watch your inbox for offers and new arrivals.');
    }

    public function about(): View
    {
        $site = $this->settings->getPublic();
        $page = $this->aboutPage->getPublicPayload();

        return view('pages.about', array_merge(
            $this->aboutMapper->toAboutView($page, $site),
            ['cartTotal' => 'Rs. 0'],
        ));
    }

    public function faq(): View
    {
        return view('pages.faq', [
            'cartTotal' => 'Rs. 0',
            'pageTitle' => 'FAQs',
            'faqs' => $this->faqs(),
        ]);
    }

    public function terms(): View
    {
        $site = $this->settings->getPublic();

        return view('pages.terms', [
            'cartTotal' => 'Rs. 0',
            'pageTitle' => 'Terms & Conditions',
            'legalBody' => $site['termsBody'],
            'lastUpdated' => $site['termsUpdated'],
        ]);
    }

    public function privacy(): View
    {
        $site = $this->settings->getPublic();

        return view('pages.privacy', [
            'cartTotal' => 'Rs. 0',
            'pageTitle' => 'Privacy Policy',
            'legalBody' => $site['privacyBody'],
            'lastUpdated' => $site['privacyUpdated'],
        ]);
    }

    public function refund(): View
    {
        $site = $this->settings->getPublic();

        return view('pages.refund', [
            'cartTotal' => 'Rs. 0',
            'pageTitle' => 'Refund Policy',
            'legalBody' => $site['refundBody'],
            'lastUpdated' => $site['refundUpdated'],
        ]);
    }

    /**
     * @return array<int, array{question: string, answer: string}>
     */
    private function faqs(): array
    {
        return [
            [
                'question' => 'What products does Mandira Foods sell?',
                'answer' => 'We offer premium dried fruits, traditional Nepali pickles, and curated gift boxes. All products are made with natural ingredients and no artificial preservatives.',
            ],
            [
                'question' => 'Do you ship outside Kathmandu?',
                'answer' => 'Yes. We deliver across Nepal through our courier partners. Delivery times vary by location — typically 2–5 business days within major cities and 5–7 days elsewhere.',
            ],
            [
                'question' => 'Is there free shipping?',
                'answer' => 'Orders above Rs. 2,000 qualify for free standard shipping within Nepal. Shipping fees for smaller orders are shown at checkout before you pay.',
            ],
            [
                'question' => 'How should I store dried fruits and pickles?',
                'answer' => 'Keep dried fruits in an airtight container in a cool, dry place. Once opened, refrigerate pickles and consume within the period noted on the label for best quality.',
            ],
            [
                'question' => 'Can I track my order?',
                'answer' => 'After your order ships, we email you a tracking link. You can also contact our support team with your order number for an update.',
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept major debit/credit cards, mobile wallets (eSewa, Khalti), and cash on delivery in selected areas. Available options appear at checkout.',
            ],
            [
                'question' => 'How do I create an account?',
                'answer' => 'Click Account in the menu or visit the Register page. An account lets you save addresses, view order history, and manage your wishlist.',
            ],
            [
                'question' => 'Who can I contact for help?',
                'answer' => 'Use the contact details on our Contact page. We usually reply within one business day.',
            ],
        ];
    }
}
