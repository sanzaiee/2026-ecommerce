@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', $pageTitle . ' — ' . ($site['siteName'] ?? 'Mandira') . ' ' . ($site['brandSuffix'] ?? 'Foods'))

@push('styles')
    <link href="{{ asset('css/content-pages.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page about-page">
        @include('partials.store.content-breadcrumb', ['title' => $pageTitle])

        {{-- Hero --}}
        <section class="about-hero" aria-labelledby="about-hero-heading">
            <div class="about-hero__media">
                <img src="{{ $images['hero'] }}" alt="Premium dried fruits from {{ $brandName }}" width="1600" height="900"
                    loading="eager" fetchpriority="high">
                <div class="about-hero__overlay" aria-hidden="true"></div>
            </div>
            <div class="container about-hero__content">
                <p class="about-hero__eyebrow">{{ $heroEyebrow }}</p>
                <h1 id="about-hero-heading" class="about-hero__title">{{ $brandName }}</h1>
                <p class="about-hero__lead">{{ $tagline }}</p>
                @if ($lastUpdated)
                    <p class="about-hero__updated">Last updated {{ $lastUpdated }}</p>
                @endif
            </div>
        </section>

        {{-- Values --}}
        <section class="about-values" aria-label="What we stand for">
            <div class="container">
                <div class="row g-4 about-values__grid">
                    @foreach ($values as $value)
                        <div class="col-6 col-lg-3">
                            <div class="about-value-card">
                                <span class="about-value-card__icon" aria-hidden="true">
                                    <i class="bi {{ $value['icon'] }}"></i>
                                </span>
                                <h2 class="about-value-card__title">{{ $value['title'] }}</h2>
                                <p class="about-value-card__text">{{ $value['text'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Story + image --}}
        <section class="about-story section-padding">
            <div class="container">
                <div class="row g-4 g-lg-5 align-items-center">
                    <div class="col-lg-6">
                        <div class="about-story__media">
                            <img src="{{ $images['story'] }}" alt="Dried fruit selection from Nepal" width="900" height="700"
                                loading="lazy">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-story__copy">
                            <h2 class="about-section-heading">{{ $storyHeading }}</h2>
                            @if ($storyParagraph1)
                                <p>{{ $storyParagraph1 }}</p>
                            @endif
                            @if ($storyParagraph2)
                                <p>{{ $storyParagraph2 }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Gallery --}}
        <section class="about-gallery section-padding section-bg" aria-label="Our products in focus">
            <div class="container">
                <div class="about-gallery__header">
                    <h2 class="about-section-heading">{{ $galleryHeading }}</h2>
                    <p class="about-section-lead">{{ $galleryLead }}</p>
                </div>
                <div class="row g-3 g-lg-4 about-gallery__grid">
                    @foreach ($images['gallery'] as $index => $item)
                        <div class="col-md-6 col-lg-4 {{ $index === 0 ? 'col-12' : '' }}">
                            <figure class="about-gallery__item {{ $index === 0 ? 'about-gallery__item--featured' : '' }}">
                                <img src="{{ $item['src'] }}" alt="{{ $item['alt'] }}" width="700" height="520" loading="lazy">
                                @if (! empty($item['caption']))
                                    <figcaption>{{ $item['caption'] }}</figcaption>
                                @endif
                            </figure>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Process --}}
        <section class="about-process section-padding">
            <div class="container">
                <div class="row g-4 g-lg-5 align-items-center">
                    <div class="col-lg-5 order-lg-2">
                        <div class="about-process__media">
                            @foreach ($images['craft'] as $craftImage)
                                <img src="{{ $craftImage['src'] }}" alt="{{ $craftImage['alt'] }}" width="800" height="600"
                                    loading="lazy">
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-7 order-lg-1">
                        <h2 class="about-section-heading">{{ $processHeading }}</h2>
                        <ol class="about-process__steps">
                            @foreach ($processSteps as $step)
                                <li>
                                    <strong>{{ $step['title'] }}</strong>
                                    <span>{{ $step['text'] }}</span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        {{-- Admin-editable body --}}
        <section class="about-editorial section-padding section-bg">
            <div class="container">
                <div class="about-editorial__inner">
                    <h2 class="about-section-heading">
                        {{ ! empty($aboutBody) ? 'More about us' : 'Our promise to you' }}
                    </h2>
                    @include('partials.store.policy-body', [
                        'legalBody' => $aboutBody,
                        'fallback' => view('partials.store.policy-fallback-about')->render(),
                    ])
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="about-cta" aria-label="Get started">
            <div class="container">
                <div class="about-cta__card">
                    <div class="about-cta__copy">
                        <h2 class="about-cta__title">{{ $ctaTitle }}</h2>
                        <p>{{ $ctaText }}</p>
                    </div>
                    <div class="about-cta__actions">
                        <a href="{{ route('shop') }}" class="btn btn-light btn-lg">Shop all products</a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg">Contact us</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
