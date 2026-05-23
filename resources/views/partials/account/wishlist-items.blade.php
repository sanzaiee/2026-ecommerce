@props([
    'items',
    'emptyTitle' => 'Your wishlist is empty',
    'emptyText' => 'Save products you love while browsing the shop. They will appear here for quick access.',
])

@php
    $total = $items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator ? $items->total() : count($items);
@endphp

<ul class="account-wishlist-grid" id="wishlistPageItems" @if ($total === 0) hidden @endif>
    @foreach ($items as $item)
        @if ($item)
            <li class="account-wishlist-item" data-product-id="{{ $item['id'] }}" data-unit-price="{{ $item['price'] }}">
                <a href="{{ $item['url'] }}" class="account-wishlist-item__media">
                    <img src="{{ $item['image'] ?: get_placeholder_image() }}" alt="{{ $item['name'] }}" loading="lazy"
                        decoding="async">
                </a>
                <div class="account-wishlist-item__body">
                    <div class="account-wishlist-item__top">
                        <h3 class="account-wishlist-item__name">
                            <a href="{{ $item['url'] }}">{{ $item['name'] }}</a>
                        </h3>
                        <button type="button" class="account-wishlist-item__remove cart-drawer__remove"
                            aria-label="Remove {{ $item['name'] }} from wishlist">
                            <i class="bi bi-trash3" aria-hidden="true"></i>
                        </button>
                    </div>
                    <p class="account-wishlist-item__price">Rs. {{ number_format((float) $item['price'], 0) }}</p>
                    @if (empty($item['inStock']))
                        <p class="account-wishlist-item__stock">Out of stock</p>
                    @endif
                    <div class="account-wishlist-item__actions">
                        <button type="button" class="account-btn account-btn--primary account-btn--sm"
                            data-action="move-to-cart" @if (empty($item['inStock'])) disabled @endif>
                            <i class="bi bi-bag-plus" aria-hidden="true"></i>
                            Move to cart
                        </button>
                        <a href="{{ $item['url'] }}" class="account-btn account-btn--outline account-btn--sm">View
                            product</a>
                    </div>
                </div>
            </li>
        @endif
    @endforeach
</ul>

<div class="account-wishlist-empty" id="wishlistPageEmpty" @if ($total > 0) hidden @endif>
    <i class="bi bi-heart" aria-hidden="true"></i>
    <h2>{{ $emptyTitle }}</h2>
    <p>{{ $emptyText }}</p>
    <a href="{{ route('shop') }}" class="account-btn account-btn--primary account-btn--inline">Browse products</a>
</div>
