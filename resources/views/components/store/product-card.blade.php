@props([
    'image',
    'name',
    'price',
    'alt' => '',
    'hoverImage' => null,
    'comparePrice' => null,
    'rating' => 0,
    'reviews' => 0,
    'outOfStock' => false,
    'onSale' => false,
    'inWishlist' => false,
    'productId' => null,
    'href' => '#',
    'categoryLabel' => null,
    'priceNumeric' => null,
])

@php
    $rating = min(5, max(0, (float) $rating));
    $productId = $productId ?? \Illuminate\Support\Str::slug($name);
    $showSaleBadge = $onSale || $comparePrice;
    $inStock = !$outOfStock;
    $imageUrl = $image ?: get_placeholder_image();
@endphp

<div {{ $attributes->merge(['class' => 'col shop-product-col']) }}>
    <article class="product-card{{ $outOfStock ? ' product-card--oos' : '' }}" data-product-id="{{ $productId }}"
        data-product-url="{{ $href }}" data-in-stock="{{ $inStock ? '1' : '0' }}"
        @if ($priceNumeric !== null) data-price="{{ $priceNumeric }}" @endif data-rating="{{ $rating }}">
        <div class="product-card__media img-wrap">
            @if ($showSaleBadge && !$outOfStock)
                <span class="product-card__badge product-card__badge--sale">Sale</span>
            @endif
            @if ($outOfStock)
                <span class="product-card__badge product-card__badge--oos">Sold out</span>
            @endif

            <a href="{{ $href }}" class="product-card__media-link" tabindex="-1" aria-hidden="true">
                <img class="product-card__img product-card__img--primary" src="{{ $imageUrl }}"
                    alt="{{ $alt ?: $name }}" loading="lazy" decoding="async">
                @if ($hoverImage)
                    <img class="product-card__img product-card__img--hover" src="{{ $hoverImage }}" alt=""
                        loading="lazy" decoding="async" aria-hidden="true">
                @endif
            </a>

            <div class="product-card__actions">
                <button type="button"
                    class="product-card__btn product-card__wishlist{{ $inWishlist ? ' is-active' : '' }}"
                    data-action="wishlist" data-product-id="{{ $productId }}"
                    data-product-name="{{ $name }}" data-product-price="{{ $priceNumeric ?? '' }}"
                    data-product-image="{{ $imageUrl }}" data-product-url="{{ $href }}"
                    data-in-stock="{{ $inStock ? '1' : '0' }}"
                    aria-label="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}"
                    aria-pressed="{{ $inWishlist ? 'true' : 'false' }}">
                    <i class="bi bi-heart{{ $inWishlist ? '-fill' : '' }}" aria-hidden="true"></i>
                </button>
            </div>

            @if ($outOfStock)
                <div class="product-card__quick-add product-card__quick-add--disabled">
                    <span>Sold out</span>
                </div>
            @else
                <button type="button" class="product-card__quick-add" data-action="add-to-cart"
                    data-product-id="{{ $productId }}" data-product-name="{{ $name }}"
                    data-product-price="{{ $priceNumeric ?? '' }}" data-product-image="{{ $imageUrl }}"
                    data-product-url="{{ $href }}" data-in-stock="1"
                    aria-label="Add {{ $name }} to cart">
                    <i class="bi bi-bag-plus" aria-hidden="true"></i>
                    <span>Add to cart</span>
                </button>
            @endif
        </div>

        <div class="card-body product-card__body">
            @if ($categoryLabel)
                <p class="product-card__vendor">{{ $categoryLabel }}</p>
            @endif

            @if ($inStock && isset($stockQuantity) && $stockQuantity <= 5)
                <p class="product-card__stock-warning">
                    <small class="text-muted">Only {{ $stockQuantity }} left!</small>
                </p>
            @endif

            @if ($reviews > 0)
                <div class="product-rating" aria-label="{{ $rating }} out of 5 stars">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= floor($rating))
                            <i class="bi bi-star-fill"></i>
                        @elseif ($i - 0.5 <= $rating)
                            <i class="bi bi-star-half"></i>
                        @else
                            <i class="bi bi-star"></i>
                        @endif
                    @endfor
                    <span class="count">({{ $reviews }})</span>
                </div>
            @endif

            <h3 class="product-name">
                <a href="{{ $href }}" class="product-card__title-link">{{ $name }}</a>
            </h3>

            <p class="product-price mb-0{{ $showSaleBadge ? ' product-price--sale' : '' }}">
                <span class="product-price__current">{{ $price }}</span>
                @if ($comparePrice)
                    <del class="product-price__compare">{{ $comparePrice }}</del>
                @endif
            </p>
        </div>
    </article>
</div>
