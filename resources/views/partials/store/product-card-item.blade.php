@php
    /** @var array<string, mixed> $product */
@endphp

<x-store.product-card
    :image="$product['image'] ?: get_placeholder_image()"
    :hover-image="$product['hoverImage'] ?: null"
    :alt="$product['alt'] ?? ''"
    :name="$product['name']"
    :price="$product['price']"
    :compare-price="$product['comparePrice'] ?? null"
    :rating="$product['rating']"
    :reviews="$product['reviews']"
    :out-of-stock="$product['outOfStock'] ?? false"
    :on-sale="$product['onSale'] ?? false"
    :category-label="$product['categoryLabel'] ?? null"
    :product-id="$product['id']"
    :href="$product['href']"
    :price-numeric="$product['priceNumeric']"
    :in-wishlist="in_array($product['id'], $wishlistSlugs ?? [], true)"
/>
