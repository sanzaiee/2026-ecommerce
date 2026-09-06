@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', $pageTitle . ' — ' . ($site['siteName'] ?? 'Mandira') . ' ' . ($site['brandSuffix'] ?? 'Foods'))

@push('styles')
    <link href="{{ asset('css/content-pages.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page blog-hub">
        @include('partials.store.content-breadcrumb', ['title' => $pageTitle])

        <section class="blog-hub__hero">
            <div class="container">
                <div class="blog-hub__hero-inner">
                    <p class="blog-hub__eyebrow">Newari tradition &amp; insights</p>
                    <h1 class="blog-hub__title">
                        @if ($activeCategory)
                            {{ $activeCategory->name }}
                        @else
                            From livelihood and craft to food, culture &amp; community
                        @endif
                    </h1>
                    <p class="blog-hub__lead">
                        @if ($activeCategory?->description)
                            {{ $activeCategory->description }}
                        @else
                            A growing library on Newari life — professions passed down through families,
                            festival rituals, courtyard culture, traditional food, and the stories elders still tell.
                            Search here when you want to understand how Newar communities live, work, and celebrate.
                        @endif
                    </p>
                </div>
            </div>
        </section>

        <section class="blog-hub__body">
            <div class="container">
                <div class="row blog-hub__layout g-4 g-lg-5">
                    <aside class="col-lg-3 d-none d-lg-block" aria-label="Blog filters">
                        <div class="blog-hub__sidebar">
                            @include('partials.store.blog-filters', [
                                'categories' => $categories,
                                'filters' => $filters,
                                'formId' => 'blogFiltersDesktop',
                                'idPrefix' => 'desktop',
                            ])
                        </div>
                    </aside>

                    <div class="col-lg-9">
                        <div class="blog-hub__toolbar">
                            <div class="blog-hub__toolbar-start">
                                @if ($filters->search)
                                    <p class="blog-hub__search-hint">
                                        Results for &ldquo;{{ $filters->search }}&rdquo;
                                    </p>
                                @endif
                                <p class="blog-hub__count">
                                    {{ $posts->total() }} {{ $posts->total() === 1 ? 'article' : 'articles' }}
                                </p>
                            </div>

                            <div class="blog-hub__toolbar-end">
                                <button
                                    type="button"
                                    class="btn blog-hub__filter-btn d-lg-none"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#blogFilterDrawer"
                                    aria-controls="blogFilterDrawer"
                                >
                                    <i class="bi bi-sliders" aria-hidden="true"></i>
                                    Filter
                                </button>
                            </div>
                        </div>

                        @if ($categories->isNotEmpty())
                            <div class="blog-hub__topics" aria-label="Newari tradition topics">
                                <a
                                    href="{{ route('blog.index') }}"
                                    @class(['blog-hub__topic', 'is-active' => ! $filters->category])
                                >
                                    All
                                </a>
                                @foreach ($categories as $category)
                                    <a
                                        href="{{ route('blog.index', ['category' => $category->slug]) }}"
                                        @class(['blog-hub__topic', 'is-active' => $filters->category === $category->slug])
                                    >
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        @if ($posts->isEmpty())
                            <div class="blog-hub__empty">
                                <div class="blog-hub__empty-icon" aria-hidden="true">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <h2 class="blog-hub__empty-title">No articles found</h2>
                                <p class="blog-hub__empty-text">
                                    @if ($filters->search || $filters->category || $filters->featured)
                                        Try a different search term or browse all topics.
                                    @else
                                        Check back soon for stories on Newari food, crafts, festivals, and culture.
                                    @endif
                                </p>
                                @if ($filters->search || $filters->category || $filters->featured)
                                    <a href="{{ route('blog.index') }}" class="btn blog-hub__empty-btn">View all articles</a>
                                @endif
                            </div>
                        @else
                            <div class="blog-hub__grid">
                                @foreach ($posts as $post)
                                    <article class="blog-card">
                                        <a class="blog-card__link" href="{{ route('blog.show', $post->slug) }}">
                                            <span class="blog-card__media">
                                                @if ($post->hasImage())
                                                    <img
                                                        class="blog-card__image"
                                                        src="{{ $post->imageUrl('medium') }}"
                                                        alt=""
                                                        width="640"
                                                        height="400"
                                                        loading="lazy"
                                                        decoding="async"
                                                    >
                                                @else
                                                    <span class="blog-card__placeholder" aria-hidden="true">
                                                        <i class="bi bi-journal-richtext"></i>
                                                    </span>
                                                @endif
                                            </span>
                                            <span class="blog-card__body">
                                                @if ($post->category)
                                                    <span class="blog-card__category">{{ $post->category->name }}</span>
                                                @endif
                                                <h2 class="blog-card__title">{{ $post->title }}</h2>
                                                <p class="blog-card__excerpt">{{ $post->summary() }}</p>
                                                <span class="blog-card__meta">
                                                    @if ($post->is_featured)
                                                        <span class="blog-card__featured">Featured</span>
                                                    @endif
                                                    <time datetime="{{ $post->created_at?->toDateString() }}">
                                                        {{ $post->created_at?->format('M j, Y') }}
                                                    </time>
                                                </span>
                                            </span>
                                        </a>
                                    </article>
                                @endforeach
                            </div>

                            @if ($posts->hasPages())
                                <div class="blog-hub__pagination">
                                    {{ $posts->withQueryString()->links() }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="offcanvas offcanvas-start blog-filter-drawer" tabindex="-1" id="blogFilterDrawer"
        aria-labelledby="blogFilterDrawerTitle">
        <div class="offcanvas-header blog-filter-drawer__header">
            <h2 class="offcanvas-title blog-filter-drawer__title" id="blogFilterDrawerTitle">Filter articles</h2>
            <button type="button" class="blog-filter-drawer__close" data-bs-dismiss="offcanvas" aria-label="Close filters">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>
        <div class="offcanvas-body blog-filter-drawer__body">
            @include('partials.store.blog-filters', [
                'categories' => $categories,
                'filters' => $filters,
                'formId' => 'blogFiltersMobile',
                'idPrefix' => 'mobile',
                'showActions' => true,
            ])
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/blog-listing.js') }}"></script>
@endpush
