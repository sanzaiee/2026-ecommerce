<?php

namespace App\Support\ViewData;

use App\Domain\Product\Models\Product;

class StorefrontBasketMapper
{
    public function __construct(private StorefrontProductMapper $products) {}

    /**
     * @return array<string, mixed>
     */
    public function lineFromProduct(Product $product, int $quantity): array
    {
        $card = $this->products->toCard($product);

        return [
            'id' => $card['id'],
            'name' => $card['name'],
            'price' => $card['priceNumeric'],
            'image' => $card['image'],
            'url' => $card['href'],
            'inStock' => $card['inStock'],
            'qty' => $quantity,
        ];
    }

    /**
     * @param  iterable<int, array{product: Product, quantity: int}>  $rows
     * @return array{items: list<array<string, mixed>>, subtotal: float, itemCount: int}
     */
    public function summarizeCart(iterable $rows): array
    {
        $items = [];
        $subtotal = 0.0;
        $itemCount = 0;

        foreach ($rows as $row) {
            $line = $this->lineFromProduct($row['product'], $row['quantity']);
            $items[] = $line;
            $subtotal += $line['price'] * $line['qty'];
            $itemCount += $line['qty'];
        }

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'itemCount' => $itemCount,
        ];
    }

    /**
     * @param  iterable<Product>  $products
     * @return array{items: list<array<string, mixed>>, count: int}
     */
    public function summarizeWishlist(iterable $products): array
    {
        $items = [];

        foreach ($products as $product) {
            $items[] = $this->lineFromProduct($product, 1);
        }

        return [
            'items' => $items,
            'count' => count($items),
        ];
    }
}
