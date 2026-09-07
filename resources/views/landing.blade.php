@extends('layouts.store', ['cartTotal' => $cartTotal])

@push('styles')
    <link href="{{ asset('css/landing.css') }}?v={{ filemtime(public_path('css/landing.css')) ?: 1 }}" rel="stylesheet">
@endpush

@push('scripts')
    <script>
        (function () {
            var targets = document.querySelectorAll(
                '.landing-section__header, .category-tile, .product-grid > .col, ' +
                '.process-step, .story-card, .culture-band, .heritage-band, .cta-band'
            );

            if (!('IntersectionObserver' in window)) {
                return;
            }

            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });

            Array.prototype.forEach.call(targets, function (el, index) {
                el.classList.add('reveal');
                el.style.transitionDelay = (index % 4) * 60 + 'ms';
                observer.observe(el);
            });
        })();
    </script>
@endpush

@section('content')
    <div class="store-home landing">
        {{-- 1. Hero --}}
        <x-store.hero :title="$hero['title']" :subtitle="$hero['subtitle']" :image="$hero['image']" :image-alt="$hero['imageAlt']" />

        {{-- 2. Shop By Category --}}
        <section class="landing-section landing-section--categories" aria-labelledby="category-heading">
            <div class="container">
                <header class="landing-section__header landing-section__header--split">
                    <div>
                        <p class="landing-eyebrow">Collections</p>
                        <h2 id="category-heading" class="landing-section__title">Shop By Category</h2>
                    </div>
                    <a href="{{ route('shop') }}" class="landing-link">
                        View All Pottery
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </header>

                <div class="category-showcase">
                    @foreach ($categories as $category)
                        <a href="{{ $category['href'] }}" class="category-tile">
                            <span class="category-tile__media">
                                <img src="{{ $category['image'] }}" alt="{{ $category['title'] }}" loading="lazy"
                                    decoding="async" width="700" height="700">
                                <span class="category-tile__scrim" aria-hidden="true"></span>
                            </span>
                            <span class="category-tile__content">
                                <h3 class="category-tile__title">{{ $category['title'] }}</h3>
                                @if (! empty($category['description']))
                                    <span class="category-tile__desc">{{ $category['description'] }}</span>
                                @endif
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

        {{-- 3a. Everyday Pottery --}}
        @if (! empty($everydayProducts))
            <section class="landing-section" aria-labelledby="everyday-heading">
                <div class="container">
                    <div class="landing-section__header landing-section__header--split">
                        <div>
                            <p class="landing-eyebrow">Handcrafted for daily life</p>
                            <h2 id="everyday-heading" class="landing-section__title">Everyday Pottery</h2>
                        </div>
                        <a href="{{ route('shop') }}" class="landing-link">
                            View All Pottery
                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="row product-grid row-cols-1 row-cols-md-2 row-cols-lg-4">
                        @foreach ($everydayProducts as $product)
                            @include('partials.store.product-card-item', ['product' => $product])
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- 3b. Most Loved Pieces --}}
        @if (! empty($topSellingProducts))
            <section class="landing-section landing-section--alt" aria-labelledby="top-selling-heading">
                <div class="container">
                    <div class="landing-section__header landing-section__header--split">
                        <div>
                            <p class="landing-eyebrow">Fired &amp; finished with care</p>
                            <h2 id="top-selling-heading" class="landing-section__title">Most Loved Pieces</h2>
                        </div>
                        <a href="{{ route('shop') }}" class="landing-link">
                            View All Pottery
                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="row product-grid row-cols-1 row-cols-md-2 row-cols-lg-4">
                        @foreach ($topSellingProducts as $product)
                            @include('partials.store.product-card-item', ['product' => $product])
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- 4. More Than Clay — cultural storytelling --}}
        <section class="landing-section landing-section--story" aria-labelledby="culture-heading">
            <div class="container">
                <div class="culture-band">
                    <div class="culture-band__copy">
                        <p class="landing-eyebrow">Our Tradition</p>
                        <h2 id="culture-heading" class="landing-section__title landing-section__title--light">More Than Clay</h2>
                        <p class="culture-band__text">
                            In the Newar households of Thimi, a clay pot is never just a pot. It carries water on
                            ordinary mornings, holds offerings during festivals, and marks the seasons of family
                            life &mdash; birth, harvest, marriage and remembrance.
                        </p>
                        <p class="culture-band__text">
                            Each vessel passes through the hands of potters whose families have shaped the same
                            clay for generations, keeping a craft that machines cannot replicate.
                        </p>
                        <a href="{{ route('about') }}" class="landing-link landing-link--light">
                            Discover Our Tradition
                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                    <ul class="culture-band__pillars" aria-label="What pottery carries">
                        <li class="culture-pillar">
                            <span class="culture-pillar__glyph" aria-hidden="true">Clay</span>
                            <span class="culture-pillar__text">River-bank earth of the Madhyapur valley, prepared by hand.</span>
                        </li>
                        <li class="culture-pillar">
                            <span class="culture-pillar__glyph" aria-hidden="true">Craft</span>
                            <span class="culture-pillar__text">The wheel, the paddle and the kiln &mdash; skills inherited, not learned from manuals.</span>
                        </li>
                        <li class="culture-pillar">
                            <span class="culture-pillar__glyph" aria-hidden="true">Community</span>
                            <span class="culture-pillar__text">Pottery squares where neighbours fire, trade and celebrate together.</span>
                        </li>
                        <li class="culture-pillar">
                            <span class="culture-pillar__glyph" aria-hidden="true">Culture</span>
                            <span class="culture-pillar__text">Vessels at the centre of Newar ritual, cuisine and hospitality.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- 5. Pottery Making Process --}}
        <section class="landing-section" aria-labelledby="process-heading">
            <div class="container">
                <header class="landing-section__header">
                    <p class="landing-eyebrow">From Earth to Vessel</p>
                    <h2 id="process-heading" class="landing-section__title">The Potter&rsquo;s Process</h2>
                    <p class="landing-section__lede">Every piece follows the same unhurried path it always has.</p>
                </header>
                <ol class="process-track">
                    <li class="process-step">
                        <span class="process-step__num" aria-hidden="true">01</span>
                        <h3 class="process-step__title">Clay</h3>
                        <p class="process-step__text">Fine river clay is collected, cleaned and wedged until it is ready for the wheel.</p>
                    </li>
                    <li class="process-step">
                        <span class="process-step__num" aria-hidden="true">02</span>
                        <h3 class="process-step__title">Shape</h3>
                        <p class="process-step__text">The potter centers the clay and draws each form upward by hand on the traditional wheel.</p>
                    </li>
                    <li class="process-step">
                        <span class="process-step__num" aria-hidden="true">03</span>
                        <h3 class="process-step__title">Dry</h3>
                        <p class="process-step__text">Vessels rest in the sun, slowly releasing moisture so they can survive the fire.</p>
                    </li>
                    <li class="process-step">
                        <span class="process-step__num" aria-hidden="true">04</span>
                        <h3 class="process-step__title">Fire</h3>
                        <p class="process-step__text">An open kiln firing hardens the clay and gives each piece its earthen tone.</p>
                    </li>
                    <li class="process-step">
                        <span class="process-step__num" aria-hidden="true">05</span>
                        <h3 class="process-step__title">Finish</h3>
                        <p class="process-step__text">Fired pieces are polished, checked and readied for kitchens and altars alike.</p>
                    </li>
                </ol>
            </div>
        </section>

        {{-- 6. From Thimi, With Tradition --}}
        <section class="landing-section landing-section--heritage" aria-labelledby="heritage-heading">
            <div class="container">
                <div class="heritage-band">
                    <p class="landing-eyebrow">Our Home</p>
                    <h2 id="heritage-heading" class="landing-section__title">From Thimi, With Tradition</h2>
                    <p class="heritage-band__text">
                        Thimi &mdash; Madhyapur &mdash; lies between Kathmandu and Bhaktapur, a valley town where
                        the Newar community has kept the potter&rsquo;s wheel turning for centuries. Here pottery
                        is a shared inheritance: the knowledge moves from parent to child, the workshops sit
                        beside homes, and the rhythm of making follows the rhythm of the seasons.
                    </p>
                    <p class="heritage-band__text">
                        When you hold one of these vessels, you hold a piece of that continuity &mdash;
                        a living craft from a town built on clay.
                    </p>
                </div>
            </div>
        </section>

        {{-- 7. Stories From the Tradition --}}
        @if (! empty($stories))
            <section class="landing-section landing-section--alt" aria-labelledby="stories-heading">
                <div class="container">
                    <div class="landing-section__header landing-section__header--split">
                        <div>
                            <p class="landing-eyebrow">Journal</p>
                            <h2 id="stories-heading" class="landing-section__title">Stories From the Tradition</h2>
                        </div>
                        <a href="{{ route('blog.index') }}" class="landing-link">
                            All Stories
                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="stories-grid">
                        @foreach ($stories as $story)
                            <x-store.story-card
                                :title="$story['title']"
                                :excerpt="$story['excerpt']"
                                :image="$story['image']"
                                :category="$story['category']"
                                :date="$story['date']"
                                :url="$story['url']"
                            />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- 8. Testimonials --}}
        @if (! empty($testimonials))
            <section class="landing-section" aria-labelledby="testimonials-heading">
                <div class="container">
                    <header class="landing-section__header">
                        <p class="landing-eyebrow">Voices</p>
                        <h2 id="testimonials-heading" class="landing-section__title">What Our Customers Say</h2>
                    </header>
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

        {{-- Closing CTA --}}
        <section class="landing-section landing-section--cta" aria-labelledby="cta-heading">
            <div class="container">
                <div class="cta-band">
                    <h2 id="cta-heading" class="cta-band__title">Bring Home a Piece of Thimi</h2>
                    <p class="cta-band__text">Handmade pottery, shipped with care from the potters of Madhyapur.</p>
                    <a href="{{ route('shop') }}" class="hero-button">
                        <span class="hero-button__text">Explore Pottery</span>
                        <span class="hero-button__icon" aria-hidden="true">
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection