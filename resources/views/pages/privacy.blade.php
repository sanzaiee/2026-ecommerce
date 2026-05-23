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
                    <h1>Privacy Policy</h1>
                    <p class="static-page__meta">Last updated: {{ $lastUpdated }}</p>
                </header>

                <article class="static-page__body">
                    <p>
                        Mandira Foods respects your privacy. This policy explains what personal information we collect,
                        how we use it, and the choices you have.
                    </p>

                    <h2>1. Information we collect</h2>
                    <ul>
                        <li><strong>Account data:</strong> name, email address, and password (stored securely hashed).</li>
                        <li><strong>Order data:</strong> shipping address, phone number, order history, and payment status.</li>
                        <li><strong>Technical data:</strong> IP address, browser type, and cookies used to run the site and remember preferences.</li>
                        <li><strong>Communications:</strong> messages you send to customer support.</li>
                    </ul>

                    <h2>2. How we use your information</h2>
                    <p>We use your data to:</p>
                    <ul>
                        <li>Process and deliver orders</li>
                        <li>Manage your account and wishlist</li>
                        <li>Send order updates and service-related emails</li>
                        <li>Improve our website and customer experience</li>
                        <li>Comply with legal obligations</li>
                    </ul>

                    <h2>3. Marketing</h2>
                    <p>
                        With your consent, we may send promotional emails about new products or offers. You can unsubscribe
                        at any time using the link in our emails or by contacting support.
                    </p>

                    <h2>4. Sharing with third parties</h2>
                    <p>
                        We do not sell your personal information. We may share limited data with:
                    </p>
                    <ul>
                        <li>Payment processors to complete transactions</li>
                        <li>Courier partners to deliver your orders</li>
                        <li>Service providers who help us operate the website (under confidentiality agreements)</li>
                    </ul>

                    <h2>5. Data retention</h2>
                    <p>
                        We keep account and order records as long as needed to provide services, resolve disputes, and meet
                        legal requirements. You may request deletion of your account subject to outstanding orders or legal
                        holds.
                    </p>

                    <h2>6. Security</h2>
                    <p>
                        We use industry-standard measures including HTTPS, secure password hashing, and access controls.
                        No method of transmission over the internet is 100% secure; we encourage strong, unique passwords.
                    </p>

                    <h2>7. Cookies</h2>
                    <p>
                        Cookies help the site function (e.g. cart, login session). You can control cookies through your
                        browser settings; disabling some cookies may limit site features.
                    </p>

                    <h2>8. Your rights</h2>
                    <p>
                        You may request access to, correction of, or deletion of your personal data by emailing
                        <a href="mailto:privacy@mandirafoods.com">privacy@mandirafoods.com</a>. We will respond within a
                        reasonable timeframe.
                    </p>

                    <h2>9. Children</h2>
                    <p>
                        Our services are not directed at children under 16. We do not knowingly collect data from minors.
                    </p>

                    <h2>10. Policy updates</h2>
                    <p>
                        We may revise this policy and will update the &ldquo;Last updated&rdquo; date. Material changes may
                        be communicated by email or a notice on the website.
                    </p>

                    <h2>11. Contact</h2>
                    <p>
                        For privacy questions, contact
                        <a href="mailto:privacy@mandirafoods.com">privacy@mandirafoods.com</a>.
                    </p>
                </article>
            </div>
        </section>
    </div>
@endsection
