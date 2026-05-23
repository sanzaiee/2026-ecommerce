@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', $pageTitle . ' — Our site')

@push('styles')
    <link href="{{ asset('css/content-pages.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page">
        @include('partials.store.content-breadcrumb', ['title' => $pageTitle])

        <section class="static-page">
            <div class="container">
                <header class="static-page__header">
                    <h1>Frequently Asked Questions</h1>
                    <p class="static-page__meta">Quick answers about ordering, shipping, and our products.</p>
                </header>

                <div class="faq-list" id="faqAccordion">
                    @foreach ($faqs as $index => $faq)
                        <div class="faq-item">
                            <button class="faq-item__btn collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq-{{ $index }}" aria-expanded="false"
                                aria-controls="faq-{{ $index }}">
                                <span>{{ $faq['question'] }}</span>
                                <i class="bi bi-chevron-down" aria-hidden="true"></i>
                            </button>
                            <div id="faq-{{ $index }}" class="collapse" data-bs-parent="#faqAccordion">
                                <div class="faq-item__body">
                                    {{ $faq['answer'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="faq-contact">
                    <h3>Still have questions?</h3>
                    <p>Our team is happy to help with orders, products, or delivery.</p>
                    <a href="mailto:support@mandirafoods.com">
                        <i class="bi bi-envelope" aria-hidden="true"></i>
                        Contact support
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
