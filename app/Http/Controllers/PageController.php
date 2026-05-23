<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PageController extends Controller
{
    public function contact(): View
    {
        return view('pages.contact', [
            'cartTotal' => 'Rs. 0',
            'pageTitle' => 'Contact Us',
            'contactInfo' => $this->contactInfo(),
        ]);
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        return redirect()
            ->route('contact')
            ->with('status', 'Thank you! We received your message and will reply within one business day.');
    }

    public function subscribeNewsletter(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'newsletter')
                ->withInput();
        }

        return back()->with('newsletter_status', 'You are subscribed! Watch your inbox for offers and new arrivals.');
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
        return view('pages.terms', [
            'cartTotal' => 'Rs. 0',
            'pageTitle' => 'Terms & Conditions',
            'lastUpdated' => 'May 23, 2026',
        ]);
    }

    public function privacy(): View
    {
        return view('pages.privacy', [
            'cartTotal' => 'Rs. 0',
            'pageTitle' => 'Privacy Policy',
            'lastUpdated' => 'May 23, 2026',
        ]);
    }

    public function refund(): View
    {
        return view('pages.refund', [
            'cartTotal' => 'Rs. 0',
            'pageTitle' => 'Refund Policy',
            'lastUpdated' => 'May 23, 2026',
        ]);
    }

    /**
     * @return array<int, array{icon: string, label: string, value: string, href: string|null}>
     */
    private function contactInfo(): array
    {
        return [
            [
                'icon' => 'bi-envelope',
                'label' => 'Email',
                'value' => 'support@mandirafoods.com',
                'href' => 'mailto:support@mandirafoods.com',
            ],
            [
                'icon' => 'bi-telephone',
                'label' => 'Phone',
                'value' => '+977 1-XXXXXXX',
                'href' => 'tel:+9771XXXXXXX',
            ],
            [
                'icon' => 'bi-geo-alt',
                'label' => 'Address',
                'value' => 'Baluwatar, Kathmandu, Nepal',
                'href' => null,
            ],
            [
                'icon' => 'bi-clock',
                'label' => 'Hours',
                'value' => 'Sun–Fri, 10:00 AM – 6:00 PM',
                'href' => null,
            ],
        ];
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
                'answer' => 'Email us at support@mandirafoods.com or call +977 1-XXXXXXX (Sun–Fri, 10 AM–6 PM). We usually reply within one business day.',
            ],
        ];
    }
}
