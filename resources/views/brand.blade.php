@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', $brand->name . ' — Our site')

@push('styles')
    <link href="{{ asset('css/shop-listing.css') }}" rel="stylesheet">
@endpush

@section('content')
    <main class="shop-page" id="brandPage">
        <div class="shop-page__breadcrumb">
            <div class="container">
                <nav aria-label="Breadcrumb">
                    <ol class="breadcrumb shop-breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shop') }}">Shop</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $brand->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <section class="category-hero" aria-labelledby="brandHeroTitle">
            @if ($brand->hasImage())
                <div class="category-hero__media" aria-hidden="true">
                    <img src="{{ $brand->imageUrl('medium') }}" alt="" loading="eager" decoding="async">
                </div>
            @endif
            <div class="category-hero__overlay" aria-hidden="true"></div>
            <div class="container category-hero__inner">
                <p class="category-hero__eyebrow">Brand</p>
                <h1 class="category-hero__title" id="brandHeroTitle">{{ $brand->name }}</h1>
                @if ($brand->description)
                    <p class="category-hero__desc">{{ $brand->description }}</p>
                @endif
                <p class="category-hero__meta">
                    <span id="shopResultCount" aria-live="polite">
                        {{ $productTotal }} {{ $productTotal === 1 ? 'product' : 'products' }}
                    </span>
                </p>
            </div>
        </section>

        <div class="container shop-page__container">
            <div class="row shop-layout g-4 g-lg-5">
                <aside class="col-lg-3 d-none d-lg-block shop-layout__sidebar" aria-label="Product filters">
                    <div class="shop-sidebar">
                        @include('partials.store.category-filters', [
                            'idPrefix' => 'desktop',
                            'formId' => 'shopFiltersDesktop',
                        ])
                    </div>
                </aside>

                <div class="col-lg-9 shop-layout__main">
                    <div class="shop-toolbar category-toolbar">
                        <div class="shop-toolbar__start">
                            <label for="brandSearch" class="visually-hidden">Search in {{ $brand->name }}</label>
                            <div class="category-toolbar__search">
                                <i class="bi bi-search" aria-hidden="true"></i>
                                <input type="search" id="brandSearch" class="category-toolbar__search-input"
                                    placeholder="Search in this brand…" data-category-search autocomplete="off">
                            </div>
                        </div>

                        <div class="shop-toolbar__end">
                            <button type="button" class="btn shop-toolbar__filter-btn d-lg-none" data-bs-toggle="offcanvas"
                                data-bs-target="#shopFilterDrawer" aria-controls="shopFilterDrawer">
                                <i class="bi bi-sliders" aria-hidden="true"></i>
                                Filter
                                <span class="shop-toolbar__filter-badge" id="shopFilterBadge" hidden>0</span>
                            </button>

                            <div class="shop-toolbar__sort">
                                <label for="shopSort" class="visually-hidden">Sort products</label>
                                <select id="shopSort" class="shop-toolbar__select" data-shop-sort>
                                    <option value="default">Featured</option>
                                    <option value="price-asc">Price: Low to High</option>
                                    <option value="price-desc">Price: High to Low</option>
                                    <option value="rating-desc">Top Rated</option>
                                </select>
                            </div>

                            <div class="shop-toolbar__view" role="group" aria-label="View layout">
                                <button type="button" class="shop-view-btn is-active" data-view="grid" aria-pressed="true"
                                    aria-label="Grid view">
                                    <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="shop-view-btn" data-view="list" aria-pressed="false"
                                    aria-label="List view">
                                    <i class="bi bi-list-ul" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="shop-active-filters" id="shopActiveFilters" hidden></div>

                    <div class="row product-grid shop-product-grid row-cols-1 row-cols-md-2 row-cols-xl-3"
                        id="shopProductGrid" data-shop-grid>
                        @foreach ($products as $product)
                            <x-store.product-card :image="$product['image']" :hover-image="$product['hoverImage'] ?? null" :alt="$product['alt'] ?? ''" :name="$product['name']"
                                :price="$product['price']" :compare-price="$product['comparePrice'] ?? null" :rating="$product['rating']" :reviews="$product['reviews']" :out-of-stock="$product['outOfStock'] ?? false"
                                :on-sale="$product['onSale'] ?? false" :category-label="$product['categoryLabel'] ?? null" :product-id="$product['id']" :href="$product['href']"
                                :price-numeric="$product['priceNumeric']" :in-wishlist="in_array($product['id'], $wishlistSlugs ?? [], true)" data-category="{{ $product['category'] }}"
                                data-price="{{ $product['priceNumeric'] }}" data-rating="{{ $product['rating'] }}"
                                data-in-stock="{{ $product['inStock'] ?? true ? '1' : '0' }}" class="shop-product-col" />
                        @endforeach
                    </div>

                    <div class="shop-empty" id="shopEmpty" @if (count($products)) hidden @endif>
                        <div class="shop-empty__icon" aria-hidden="true">
                            <i class="bi bi-search"></i>
                        </div>
                        <h2 class="shop-empty__title">No products found</h2>
                        <p class="shop-empty__text" id="shopEmptyText">
                            Try adjusting your filters or clearing them to see more items.
                        </p>
                        <button type="button" class="btn shop-empty__btn" data-action="clear-filters">
                            Clear all filters
                        </button>
                    </div>

                    <div class="shop-load-more" id="shopLoadMoreWrap">
                        <button type="button" class="btn shop-load-more__btn" id="shopLoadMore" hidden>
                            Load more
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="offcanvas offcanvas-start shop-filter-drawer" tabindex="-1" id="shopFilterDrawer"
        aria-labelledby="shopFilterDrawerTitle">
        <div class="offcanvas-header shop-filter-drawer__header">
            <h2 class="offcanvas-title shop-filter-drawer__title" id="shopFilterDrawerTitle">Filters</h2>
            <button type="button" class="shop-filter-drawer__close" data-bs-dismiss="offcanvas"
                aria-label="Close filters">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>
        <div class="offcanvas-body shop-filter-drawer__body">
            @include('partials.store.category-filters', [
                'idPrefix' => 'mobile',
                'formId' => 'shopFiltersMobile',
                'showActions' => true,
            ])
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/shop-listing.js') }}"></script>
@endpush
