<?php

namespace App\Support\ViewData;

use App\Domain\Product\Models\Product;

class StorefrontProductMapper
{
    /**
     * @return array<string, mixed>
     */
    public function toCard(Product $product): array
    {
        $gallery = $product->galleryMedia();
        $image = $product->primaryImageUrl('medium');
        $hover = $gallery->skip(1)->first()?->getUrl('medium') ?? $image;

        return [
            'id' => $product->slug,
            'slug' => $product->slug,
            'image' => $image,
            'hoverImage' => $hover,
            'alt' => $product->title,
            'name' => $product->title,
            'category' => $product->category?->slug,
            'categoryLabel' => $product->category?->name,
            'price' => $this->formatPrice($product->price),
            'priceNumeric' => (float) $product->price,
            'comparePrice' => $product->old_price ? $this->formatPrice($product->old_price) : null,
            'comparePriceNumeric' => $product->old_price ? (float) $product->old_price : null,
            'onSale' => $product->isOnSale(),
            'rating' => (float) $product->rating_avg,
            'reviews' => $product->review_count,
            'inStock' => $product->inStock(),
            'outOfStock' => ! $product->inStock(),
            'stockQuantity' => $product->stock_quantity,
            'href' => route('product.show', $product->slug),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toLandingCard(Product $product): array
    {
        $card = $this->toCard($product);

        return [
            'id' => $card['id'],
            'image' => $card['image'],
            'hoverImage' => $card['hoverImage'],
            'alt' => $card['alt'],
            'name' => $card['name'],
            'price' => $card['price'],
            'priceNumeric' => $card['priceNumeric'],
            'comparePrice' => $card['comparePrice'],
            'rating' => $card['rating'],
            'reviews' => $card['reviews'],
            'outOfStock' => $card['outOfStock'] ?? false,
            'onSale' => $card['onSale'] ?? false,
            'categoryLabel' => $card['categoryLabel'],
            'href' => $card['href'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDetail(Product $product): array
    {
        $images = $product->galleryUrls('large');

        if ($images === []) {
            $images = [''];
        }

        $discount = null;
        if ($product->old_price && $product->old_price > $product->price) {
            $discount = (int) round((1 - ($product->price / $product->old_price)) * 100);
        }

        return [
            'id' => $product->slug,
            'slug' => $product->slug,
            'name' => $product->title,
            'category' => $product->category?->name ?? '',
            'categorySlug' => $product->category?->slug,
            'brand' => $product->brand?->name,
            'brandSlug' => $product->brand?->slug,
            'price' => (float) $product->price,
            'priceFormatted' => $this->formatPrice($product->price),
            'comparePrice' => $product->old_price ? (float) $product->old_price : null,
            'comparePriceFormatted' => $product->old_price ? $this->formatPrice($product->old_price) : null,
            'discountPercent' => $discount,
            'inStock' => $product->inStock(),
            'stockQuantity' => $product->stock_quantity,
            'onSale' => $product->isOnSale(),
            'rating' => (float) $product->rating_avg,
            'reviewCount' => $product->review_count,
            'shortDescription' => $product->short_description ?? '',
            'images' => $images,
            'description' => $product->description ? explode("\n\n", $product->description) : [],
            'bullets' => $product->bullets ?? [],
            'additionalInfo' => $product->additional_info ?? [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toRelated(Product $product): array
    {
        $card = $this->toCard($product);

        return [
            'id' => $card['id'],
            'image' => $card['image'],
            'hoverImage' => $card['hoverImage'],
            'alt' => $card['alt'],
            'name' => $card['name'],
            'price' => $card['price'],
            'priceNumeric' => $card['priceNumeric'],
            'comparePrice' => $card['comparePrice'],
            'rating' => $card['rating'],
            'reviews' => $card['reviews'],
            'outOfStock' => $card['outOfStock'] ?? false,
            'onSale' => $card['onSale'] ?? false,
            'href' => $card['href'],
        ];
    }

    private function formatPrice(float|string $amount): string
    {
        return 'Rs. '.number_format((float) $amount, 0);
    }
}
