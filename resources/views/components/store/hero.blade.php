@props([
    'title' => 'Handmade Newar pottery from Thimi — for home, garden and ritual.',
    'subtitle' => 'Terracotta planting pots, clay water vessels and everyday ware — shaped on the wheel in Madhyapur.',
    'image' => null,
    'imageAlt' => 'Handmade Newar pottery from Thimi, Nepal',
])

@php
    $heroImage = $image ?: get_placeholder_image();
@endphp

<section class="hero-section">
    <div class="hero-section__media">
        <img src="{{ $heroImage }}" alt="{{ $imageAlt }}" loading="eager" fetchpriority="high" decoding="async"
            width="1600" height="900">
    </div>
    <div class="hero-section__ambient" aria-hidden="true"></div>

    <div class="container">
        <div class="hero-grid">
            <div class="hero-content">
                <p class="hero-eyebrow">Jheekuma &mdash; Thimi, Nepal</p>
                <h1 class="hero-title">{{ $title }}</h1>
                <p class="hero-subtitle">{{ $subtitle }}</p>
                <div class="hero-accent-line" aria-hidden="true"></div>
                <div class="hero-actions">
                    <a href="{{ route('shop') }}" class="hero-button">
                        <span class="hero-button__text">Shop Pottery</span>
                        <span class="hero-button__icon" aria-hidden="true">
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </a>
                    <a href="{{ route('about') }}" class="hero-button--secondary">Meet the Potters</a>
                </div>
                <a href="#category-heading" class="hero-scroll">
                    Scroll to explore
                    <i class="bi bi-arrow-down" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</section>
