@props([
    'title' => 'Mandira Premium Dried Fruits',
    'subtitle' => 'All natural. No added sugar, color.',
    'image' => 'https://images.unsplash.com/photo-1608797178974-15b35a8edeaa?w=800&q=80',
    'imageAlt' => 'Mixed dried fruits assortment',
])

<section class="hero-section" {{ $attributes }}>
    <div class="hero-section__glow" aria-hidden="true"></div>
    <div class="container hero-section__container">
        <div class="hero-grid">
            <div class="hero-content">
                <div class="hero-copy">
                    <h1 class="hero-title">{{ $title }}</h1>
                    <p class="hero-subtitle">{{ $subtitle }}</p>
                    <div class="hero-accent-line" aria-hidden="true"></div>
                    <a href="{{ route('about') }}" class="hero-button">
                        <span class="hero-button__text">Know More</span>
                        <span class="hero-button__icon" aria-hidden="true">
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </a>
                </div>
            </div>

            <div class="hero-visual">
                <div class="hero-visual__orb hero-visual__orb--one" aria-hidden="true"></div>
                <div class="hero-visual__orb hero-visual__orb--two" aria-hidden="true"></div>
                <div class="hero-image-wrap">
                    <img src="{{ $image }}" alt="{{ $imageAlt }}" loading="eager" decoding="async">
                </div>
            </div>
        </div>
    </div>
</section>
