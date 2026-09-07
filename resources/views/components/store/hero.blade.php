@props([
    'title' => 'Where Clay Becomes Culture',
    'subtitle' => 'Discover traditional clay pottery shaped by generations of Newar craftsmanship in Thimi, Nepal.',
    'image' => null,
    'imageAlt' => 'Traditional Newar clay pottery from Thimi, Nepal',
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
                        <span class="hero-button__text">Explore Pottery</span>
                        <span class="hero-button__icon" aria-hidden="true">
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </a>
                    <a href="{{ route('about') }}" class="hero-button--secondary">Discover Our Tradition</a>
                </div>
                <a href="#category-heading" class="hero-scroll">
                    Scroll to explore
                    <i class="bi bi-arrow-down" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</section>
