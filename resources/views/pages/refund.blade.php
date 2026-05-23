@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', $pageTitle . ' — Mandira Foods')

@push('styles')
    <link href="{{ asset('css/content-pages.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page">
        @include('partials.store.content-breadcrumb', ['title' => $pageTitle])

        <section class="static-page">
            <div class="container">
                <header class="static-page__header">
                    <h1>Refund Policy</h1>
                    <p class="static-page__meta">Last updated: {{ $lastUpdated }}</p>
                </header>

                <article class="static-page__body">
                    <p>
                        We want you to be satisfied with every Mandira Foods purchase. If something is not right, please
                        review this policy and contact us — we will do our best to help.
                    </p>

                    <h2>1. Eligible returns</h2>
                    <p>You may request a return or refund when:</p>
                    <ul>
                        <li>The product arrived damaged, spoiled, or clearly defective</li>
                        <li>You received the wrong item or an incomplete order</li>
                        <li>The package was lost in transit and not delivered within the stated timeframe</li>
                    </ul>

                    <h2>2. Non-returnable items</h2>
                    <p>For food safety reasons, we generally cannot accept returns of:</p>
                    <ul>
                        <li>Opened or partially consumed products (unless defective)</li>
                        <li>Items returned without original sealed packaging where applicable</li>
                        <li>Products damaged due to improper storage after delivery</li>
                        <li>Gift cards or promotional free items</li>
                    </ul>

                    <h2>3. Time limit</h2>
                    <p>
                        Contact us within <strong>48 hours</strong> of delivery for perishable items (pickles, opened dried
                        fruit packs) and within <strong>7 days</strong> for unopened non-perishable goods. Include your order
                        number and clear photos of the product and packaging.
                    </p>

                    <h2>4. How to request a refund</h2>
                    <ol>
                        <li>Email <a href="mailto:support@mandirafoods.com">support@mandirafoods.com</a> with your order number and reason.</li>
                        <li>Our team will review your request within 1–2 business days.</li>
                        <li>If approved, we may arrange a pickup or ask you to dispose of unsafe food items per our instructions.</li>
                        <li>Refunds are issued to the original payment method once the return is confirmed.</li>
                    </ol>

                    <h2>5. Refund processing time</h2>
                    <p>
                        Approved refunds are processed within 5–10 business days. Bank or wallet providers may take additional
                        time to reflect the credit in your account.
                    </p>

                    <h2>6. Exchanges</h2>
                    <p>
                        Where stock allows, we may offer a replacement instead of a refund for the same product. If you prefer
                        a different item, any price difference will be charged or refunded accordingly.
                    </p>

                    <h2>7. Shipping costs</h2>
                    <p>
                        If the return is due to our error (wrong item, damage in transit), we cover return shipping. For
                        change-of-mind returns on eligible unopened goods, return shipping may be deducted from the refund.
                    </p>

                    <h2>8. Cash on delivery orders</h2>
                    <p>
                        Refunds for COD orders are issued via bank transfer or mobile wallet to the details you provide after
                        verification.
                    </p>

                    <h2>9. Contact</h2>
                    <p>
                        Questions about returns? Visit our <a href="{{ route('faqs') }}">FAQs</a> or email
                        <a href="mailto:support@mandirafoods.com">support@mandirafoods.com</a>.
                    </p>
                </article>
            </div>
        </section>
    </div>
@endsection
