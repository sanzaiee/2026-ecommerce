@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', $product['name'] . ' — Mandira Foods')

@push('styles')
    <link href="{{ asset('css/product-detail.css') }}" rel="stylesheet">
@endpush

@section('content')
    @php
        $rating = min(5, max(0, (float) $product['rating']));
        $initialReviews = [
            [
                'name' => 'Sunita R.',
                'rating' => 5,
                'text' => 'Absolutely delicious! The mangoes are sweet, chewy, and taste incredibly fresh. Will definitely order again.',
                'date' => '2026-04-12',
            ],
            [
                'name' => 'Rajesh K.',
                'rating' => 4,
                'text' => 'Great quality and packaging. A bit pricey but worth it for the natural taste without any additives.',
                'date' => '2026-03-28',
            ],
            [
                'name' => 'Anita M.',
                'rating' => 5,
                'text' => 'My kids love these as an after-school snack. Love that there is no added sugar!',
                'date' => '2026-03-05',
            ],
        ];
    @endphp

    <div class="product-detail" id="productDetail"
        data-product-id="{{ $product['id'] }}"
        data-product-name="{{ $product['name'] }}"
        data-product-price="{{ $product['price'] }}"
        data-product-image="{{ $product['images'][0] }}"
        data-in-stock="{{ $product['inStock'] ? '1' : '0' }}">

        {{-- Breadcrumb --}}
        <nav class="product-detail__breadcrumb" aria-label="Breadcrumb">
            <div class="container">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/') }}#products">{{ $product['category'] }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $product['name'] }}</li>
                </ol>
            </div>
        </nav>

        {{-- Main product --}}
        <section class="product-detail__main section-padding pt-0">
            <div class="container">
                <div class="row g-4 g-lg-5 align-items-start">
                    {{-- Gallery --}}
                    <div class="col-lg-6">
                        <div class="product-gallery">
                            <div class="product-gallery__main">
                                <img src="{{ $product['images'][0] }}" alt="{{ $product['name'] }}"
                                    id="productMainImage" class="product-gallery__main-img">
                            </div>
                            <div class="product-gallery__thumbs" role="list" aria-label="Product images">
                                @foreach ($product['images'] as $index => $image)
                                    <button type="button"
                                        class="product-gallery__thumb{{ $index === 0 ? ' is-active' : '' }}"
                                        data-image="{{ $image }}"
                                        aria-label="View image {{ $index + 1 }}"
                                        aria-pressed="{{ $index === 0 ? 'true' : 'false' }}">
                                        <img src="{{ $image }}" alt="">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Details --}}
                    <div class="col-lg-6">
                        <div class="product-info">
                            @if ($product['onSale'] && $product['inStock'])
                                <span class="product-info__badge product-info__badge--sale">Sale</span>
                            @endif

                            <h1 class="product-info__title">{{ $product['name'] }}</h1>

                            <div class="product-rating product-info__rating"
                                aria-label="{{ $rating }} out of 5 stars, {{ $product['reviewCount'] }} reviews">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($rating))
                                        <i class="bi bi-star-fill"></i>
                                    @elseif ($i - 0.5 <= $rating)
                                        <i class="bi bi-star-half"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                                <a href="#reviews" class="product-info__review-link">
                                    ({{ $product['reviewCount'] }} reviews)
                                </a>
                            </div>

                            <p class="product-info__category">{{ $product['category'] }}</p>

                            <div class="product-info__price-row">
                                <span class="product-info__price">{{ $product['priceFormatted'] }}</span>
                                @if ($product['comparePriceFormatted'])
                                    <del class="product-info__compare">{{ $product['comparePriceFormatted'] }}</del>
                                @endif
                                @if ($product['discountPercent'])
                                    <span class="product-info__discount">{{ $product['discountPercent'] }}% OFF</span>
                                @endif
                            </div>

                            <p class="product-info__stock product-info__stock--{{ $product['inStock'] ? 'in' : 'out' }}">
                                @if ($product['inStock'])
                                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i> In Stock
                                @else
                                    <i class="bi bi-x-circle-fill" aria-hidden="true"></i> Out of Stock
                                @endif
                            </p>

                            <p class="product-info__short">{{ $product['shortDescription'] }}</p>

                            <div class="product-info__actions">
                                <div class="product-qty" role="group" aria-label="Quantity">
                                    <button type="button" class="product-qty__btn" id="qtyDecrease"
                                        aria-label="Decrease quantity" {{ $product['inStock'] ? '' : 'disabled' }}>
                                        <i class="bi bi-dash" aria-hidden="true"></i>
                                    </button>
                                    <input type="number" class="product-qty__input" id="productQty" value="1"
                                        min="1" max="99" aria-label="Quantity"
                                        {{ $product['inStock'] ? '' : 'disabled' }}>
                                    <button type="button" class="product-qty__btn" id="qtyIncrease"
                                        aria-label="Increase quantity" {{ $product['inStock'] ? '' : 'disabled' }}>
                                        <i class="bi bi-plus" aria-hidden="true"></i>
                                    </button>
                                </div>

                                <button type="button" class="product-info__wishlist" id="productWishlist"
                                    aria-label="Add to wishlist" aria-pressed="false">
                                    <i class="bi bi-heart" aria-hidden="true"></i>
                                </button>
                            </div>

                            <button type="button" class="btn product-info__add-cart" id="addToCartBtn"
                                {{ $product['inStock'] ? '' : 'disabled' }}>
                                <i class="bi bi-bag-plus" aria-hidden="true"></i>
                                {{ $product['inStock'] ? 'Add to Cart' : 'Out of Stock' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Description tabs --}}
        <section class="product-detail__tabs section-padding section-bg">
            <div class="container">
                <ul class="nav nav-tabs product-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-description" data-bs-toggle="tab"
                            data-bs-target="#panel-description" type="button" role="tab"
                            aria-controls="panel-description" aria-selected="true">Description</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-info" data-bs-toggle="tab" data-bs-target="#panel-info"
                            type="button" role="tab" aria-controls="panel-info" aria-selected="false">Additional
                            Info</button>
                    </li>
                </ul>
                <div class="tab-content product-tab-panels" id="productTabPanels">
                    <div class="tab-pane fade show active" id="panel-description" role="tabpanel"
                        aria-labelledby="tab-description">
                        @foreach ($product['description'] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                        <ul class="product-tab-list">
                            @foreach ($product['bullets'] as $bullet)
                                <li>{{ $bullet }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="panel-info" role="tabpanel" aria-labelledby="tab-info">
                        <table class="product-info-table">
                            <tbody>
                                @foreach ($product['additionalInfo'] as $row)
                                    <tr>
                                        <th scope="row">{{ $row['label'] }}</th>
                                        <td>{{ $row['value'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        {{-- Reviews --}}
        <section class="product-detail__reviews section-padding" id="reviews">
            <div class="container">
                <h2 class="section-heading-left">Customer Reviews</h2>

                <div class="row g-5">
                    <div class="col-lg-7">
                        <div class="reviews-list" id="reviewsList">
                            @foreach ($initialReviews as $review)
                                <article class="review-card">
                                    <div class="review-card__header">
                                        <strong class="review-card__name">{{ $review['name'] }}</strong>
                                        <time class="review-card__date"
                                            datetime="{{ $review['date'] }}">{{ date('M j, Y', strtotime($review['date'])) }}</time>
                                    </div>
                                    <div class="review-card__stars" aria-label="{{ $review['rating'] }} out of 5 stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $review['rating'] ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="review-card__text">{{ $review['text'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <form class="review-form" id="reviewForm" novalidate>
                            <h3 class="review-form__title">Write a Review</h3>
                            <div class="mb-3">
                                <label for="reviewName" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="reviewName" required
                                    placeholder="Enter your name">
                            </div>
                            <div class="mb-3">
                                <label for="reviewRating" class="form-label">Rating</label>
                                <select class="form-select" id="reviewRating" required>
                                    <option value="" selected disabled>Choose rating</option>
                                    <option value="5">5 — Excellent</option>
                                    <option value="4">4 — Good</option>
                                    <option value="3">3 — Average</option>
                                    <option value="2">2 — Fair</option>
                                    <option value="1">1 — Poor</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="reviewText" class="form-label">Your Review</label>
                                <textarea class="form-control" id="reviewText" rows="4" required
                                    placeholder="Share your experience with this product"></textarea>
                            </div>
                            <button type="submit" class="btn review-form__submit">Submit Review</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        {{-- Related products --}}
        <section class="product-detail__related section-padding section-bg">
            <div class="container">
                <h2 class="section-heading-left">You May Also Like</h2>
                <div class="related-products-scroll">
                    <div class="row product-grid row-cols-1 row-cols-sm-2 row-cols-lg-4 flex-nowrap flex-lg-wrap">
                        @foreach ($relatedProducts as $related)
                            <x-store.product-card
                                :image="$related['image']"
                                :alt="$related['alt'] ?? ''"
                                :name="$related['name']"
                                :price="$related['price']"
                                :compare-price="$related['comparePrice'] ?? null"
                                :rating="$related['rating']"
                                :reviews="$related['reviews']"
                                :product-id="$related['id']"
                                :href="$related['href']"
                            />
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="product-toast" id="productToast" role="status" aria-live="polite" aria-atomic="true" hidden>
        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
        <span id="productToastMessage">Added to cart</span>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/product-detail.js') }}"></script>
@endpush
