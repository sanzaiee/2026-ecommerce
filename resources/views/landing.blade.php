@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('content')
    <div class="store-home">
        <x-store.hero :title="$hero['title']" :subtitle="$hero['subtitle']" :image="$hero['image']" :image-alt="$hero['imageAlt']" />

        {{-- Shop By Category --}}
        <section class="category-section" aria-labelledby="category-heading">
            <div class="category-section__ambient" aria-hidden="true"></div>
            <div class="container category-section__container">
                <header class="category-section__header">
                    <p class="category-section__eyebrow">Collections</p>
                    <h2 id="category-heading" class="category-section__title">Shop By Category</h2>
                    <div class="category-section__accent" aria-hidden="true"></div>
                </header>

                <div class="category-showcase">
                    @foreach ($categories as $category)
                        <a href="{{ $category['href'] }}" class="category-tile">
                            <span class="category-tile__media">
                                <img src="{{ $category['image'] }}" alt="{{ $category['title'] }}" loading="lazy"
                                    decoding="async">
                                <span class="category-tile__scrim" aria-hidden="true"></span>
                            </span>
                            <span class="category-tile__content">
                                <h3 class="category-tile__title">{{ $category['title'] }}</h3>
                                <span class="category-tile__cta">
                                    Explore
                                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                </span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Everyday Healthy Bites --}}
        <section class="section-padding section-bg">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-heading-left">Everyday Healthy Bites</h2>
                </div>
                <div class="row product-grid row-cols-1 row-cols-md-2 row-cols-lg-4">
                    @foreach ($everydayProducts as $product)
                        @include('partials.store.product-card-item', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Top Selling --}}
        <section class="section-padding">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-heading-left">Top Selling</h2>
                </div>
                <div class="row product-grid row-cols-1 row-cols-md-2 row-cols-lg-4">
                    @foreach ($topSellingProducts as $product)
                        @include('partials.store.product-card-item', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Watch & Explore --}}
        {{-- <section class="section-padding section-bg reels-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-heading-left">Watch &amp; Explore</h2>
            </div>
            <div class="reels-scroll" id="reelsScroll">
                @foreach ($reels as $reel)
                    <div class="reel-card">
                        <img src="{{ $reel['src'] }}" alt="{{ $reel['alt'] }}">
                        <div class="play-overlay">
                            <span class="play-btn"><i class="bi bi-play-fill"></i></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section> --}}

        @if (!empty($testimonials))
            <section class="section-padding section-bg testimonials-section" aria-labelledby="testimonials-heading">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-heading-left" id="testimonials-heading">What Our Customers Say</h2>
                    </div>
                    <div class="row g-4 testimonials-grid">
                        @foreach ($testimonials as $testimonial)
                            @include('partials.store.testimonial-card', ['testimonial' => $testimonial])
                        @endforeach
                    </div>
                    <p class="testimonials-section__about text-center mb-0">
                        <a href="{{ route('about') }}">Learn more about Our site</a>
                    </p>
                </div>
            </section>
        @endif
    </div>
@endsection
