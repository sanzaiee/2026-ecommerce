@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('content')
    <x-store.hero
        :title="$hero['title']"
        :subtitle="$hero['subtitle']"
        :image="$hero['image']"
        :image-alt="$hero['imageAlt']"
    />

    {{-- Shop By Category --}}
    <section class="section-padding">
        <div class="container">
            <div class="section-header section-header--center">
                <h2 class="section-heading">Shop By Category</h2>
            </div>
            <div class="row category-row justify-content-center">
                @foreach ($categories as $category)
                    <div class="col-md-6">
                        <a href="{{ $category['href'] }}" class="category-card">
                            <div class="img-wrap">
                                <img src="{{ $category['image'] }}" alt="{{ $category['title'] }}">
                            </div>
                            <h3 class="card-title">{{ $category['title'] }}</h3>
                        </a>
                    </div>
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
                    <x-store.product-card
                        :image="$product['image']"
                        :alt="$product['alt'] ?? ''"
                        :name="$product['name']"
                        :price="$product['price']"
                        :compare-price="$product['comparePrice'] ?? null"
                        :rating="$product['rating']"
                        :reviews="$product['reviews']"
                        :out-of-stock="$product['outOfStock'] ?? false"
                        :href="$product['href'] ?? url('/products/premium-dried-mango-slices')"
                    />
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
                    <x-store.product-card
                        :image="$product['image']"
                        :alt="$product['alt'] ?? ''"
                        :name="$product['name']"
                        :price="$product['price']"
                        :compare-price="$product['comparePrice'] ?? null"
                        :rating="$product['rating']"
                        :reviews="$product['reviews']"
                        :out-of-stock="$product['outOfStock'] ?? false"
                        :href="$product['href'] ?? url('/products/premium-dried-mango-slices')"
                    />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Watch & Explore --}}
    <section class="section-padding section-bg reels-section">
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
    </section>
@endsection
