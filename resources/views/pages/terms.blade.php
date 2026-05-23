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
                    <h1>Terms &amp; Conditions</h1>
                    <p class="static-page__meta">Last updated: {{ $lastUpdated }}</p>
                </header>

                <article class="static-page__body">
                    <p>
                        Welcome to Mandira Foods. By accessing our website or placing an order, you agree to these Terms
                        &amp; Conditions. Please read them carefully before using our services.
                    </p>

                    <h2>1. About us</h2>
                    <p>
                        Mandira Foods sells premium dried fruits, traditional pickles, and related products online within
                        Nepal. Our registered business operates from Kathmandu, Nepal. References to &ldquo;we,&rdquo;
                        &ldquo;us,&rdquo; or &ldquo;Mandira Foods&rdquo; mean our company and website.
                    </p>

                    <h2>2. Account registration</h2>
                    <p>
                        You must provide accurate information when creating an account. You are responsible for keeping
                        your password secure and for all activity under your account. Notify us immediately if you suspect
                        unauthorized use.
                    </p>

                    <h2>3. Orders and pricing</h2>
                    <ul>
                        <li>All prices are listed in Nepalese Rupees (NPR) unless stated otherwise.</li>
                        <li>We reserve the right to correct pricing errors and to cancel orders affected by such errors.</li>
                        <li>An order is confirmed only after you receive an order confirmation email.</li>
                        <li>Product images are for illustration; slight variation in natural products may occur.</li>
                    </ul>

                    <h2>4. Payment</h2>
                    <p>
                        Payment must be completed using the methods offered at checkout. For cash on delivery, full payment
                        is due upon delivery. We do not store full card details on our servers.
                    </p>

                    <h2>5. Shipping and delivery</h2>
                    <p>
                        Estimated delivery times are provided at checkout and may vary by location. Risk of loss passes to
                        you upon delivery to the address you provide. You are responsible for providing a correct and
                        reachable delivery address and phone number.
                    </p>

                    <h2>6. Product quality</h2>
                    <p>
                        We source and pack products to high standards. Consumption is at your own discretion if you have
                        allergies or dietary restrictions. Always read ingredient labels before use.
                    </p>

                    <h2>7. Returns and refunds</h2>
                    <p>
                        Returns and refunds are governed by our separate <a href="{{ route('refund') }}">Refund Policy</a>.
                        By placing an order, you agree to those terms.
                    </p>

                    <h2>8. Intellectual property</h2>
                    <p>
                        All content on this website — including text, logos, images, and design — is owned by Mandira Foods
                        or its licensors and may not be copied without written permission.
                    </p>

                    <h2>9. Limitation of liability</h2>
                    <p>
                        To the fullest extent permitted by law, Mandira Foods is not liable for indirect, incidental, or
                        consequential damages arising from use of our website or products. Our total liability for any
                        claim shall not exceed the amount you paid for the relevant order.
                    </p>

                    <h2>10. Changes</h2>
                    <p>
                        We may update these terms from time to time. Continued use of the site after changes constitutes
                        acceptance of the revised terms.
                    </p>

                    <h2>11. Contact</h2>
                    <p>
                        Questions about these terms? Email <a href="mailto:legal@mandirafoods.com">legal@mandirafoods.com</a>.
                    </p>
                </article>
            </div>
        </section>
    </div>
@endsection
