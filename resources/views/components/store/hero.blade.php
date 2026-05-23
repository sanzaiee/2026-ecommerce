@props([
    'title' => 'Mandira Premium Dried Fruits',
    'subtitle' => 'All natural. No added sugar, color.',
    'image' => 'https://images.unsplash.com/photo-1608797178974-15b35a8edeaa?w=800&q=80',
    'imageAlt' => 'Mixed dried fruits assortment',
])

<section class="hero-section" {{ $attributes }}>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-1 order-2">
                <div class="hero-image-wrap">
                    <img src="{{ $image }}" alt="{{ $imageAlt }}">
                </div>
            </div>
            <div class="col-lg-6 order-lg-2 order-1">
                <div class="hero-content">
                    <h1 class="hero-title">{{ $title }}</h1>
                    <p class="hero-subtitle">{{ $subtitle }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
