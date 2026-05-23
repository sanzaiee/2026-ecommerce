@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', $pageTitle . ' — Our site')

@push('styles')
    <link href="{{ asset('css/content-pages.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page">
        @include('partials.store.content-breadcrumb', ['title' => $pageTitle])

        <section class="static-page contact-page">
            <div class="container">
                <header class="static-page__header">
                    <h1>Contact Us</h1>
                    <p class="static-page__meta">Questions about orders, products, or wholesale? We are here to help.</p>
                </header>

                @if (session('status'))
                    <div class="alert alert-success contact-alert" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="row g-4 g-lg-5">
                    <div class="col-lg-5">
                        <div class="contact-info-grid">
                            @foreach ($contactInfo as $item)
                                <div class="contact-info-card">
                                    <div class="contact-info-card__icon">
                                        <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>
                                    </div>
                                    <div>
                                        <span class="contact-info-card__label">{{ $item['label'] }}</span>
                                        @if ($item['href'])
                                            <a href="{{ $item['href'] }}"
                                                class="contact-info-card__value">{{ $item['value'] }}</a>
                                        @else
                                            <p class="contact-info-card__value mb-0">{{ $item['value'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <p class="contact-page__note">
                            For order issues, include your order number. See our
                            <a href="{{ route('faqs') }}">FAQs</a> for quick answers.
                        </p>
                    </div>

                    <div class="col-lg-7">
                        <div class="contact-form-card">
                            <h2>Send us a message</h2>
                            <form class="contact-form" method="POST" action="{{ route('contact.submit') }}" novalidate>
                                @csrf

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Full name</label>
                                        <input type="text" id="name" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" required placeholder="Your name">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" id="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" required placeholder="you@example.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="phone" class="form-label">Phone <span
                                                class="text-muted">(optional)</span></label>
                                        <input type="tel" id="phone" name="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone') }}" placeholder="+977 98XXXXXXXX">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="subject" class="form-label">Subject</label>
                                        <select id="subject" name="subject"
                                            class="form-select @error('subject') is-invalid @enderror" required>
                                            <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Choose a
                                                topic</option>
                                            <option value="Order inquiry" @selected(old('subject') === 'Order inquiry')>Order inquiry
                                            </option>
                                            <option value="Product question" @selected(old('subject') === 'Product question')>Product question
                                            </option>
                                            <option value="Shipping & delivery" @selected(old('subject') === 'Shipping & delivery')>Shipping &
                                                delivery</option>
                                            <option value="Returns & refunds" @selected(old('subject') === 'Returns & refunds')>Returns & refunds
                                            </option>
                                            <option value="Wholesale / bulk" @selected(old('subject') === 'Wholesale / bulk')>Wholesale / bulk
                                            </option>
                                            <option value="Other" @selected(old('subject') === 'Other')>Other</option>
                                        </select>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea id="message" name="message" rows="5" class="form-control @error('message') is-invalid @enderror"
                                            required placeholder="How can we help you?">{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="auth-btn">Send message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
