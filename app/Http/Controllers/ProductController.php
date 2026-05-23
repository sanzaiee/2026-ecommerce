<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(?string $slug = null): View
    {
        $product = $this->resolveProduct($slug);

        return view('product', [
            'cartTotal' => 'Rs. 1,130',
            'product' => $product,
            'relatedProducts' => $this->relatedProducts($product['id']),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveProduct(?string $slug): array
    {
        $catalog = $this->catalog();

        if ($slug !== null && isset($catalog[$slug])) {
            return $catalog[$slug];
        }

        return $catalog['premium-dried-mango-slices'];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function catalog(): array
    {
        return [
            'premium-dried-mango-slices' => [
                'id' => 'premium-dried-mango-slices',
                'slug' => 'premium-dried-mango-slices',
                'name' => 'Premium Dried Mango Slices',
                'category' => 'Dried Fruit',
                'price' => 450,
                'priceFormatted' => 'Rs. 450',
                'comparePrice' => 560,
                'comparePriceFormatted' => 'Rs. 560',
                'discountPercent' => 20,
                'inStock' => true,
                'onSale' => true,
                'rating' => 4.5,
                'reviewCount' => 24,
                'shortDescription' => 'Sun-ripened Alphonso mangoes, slow-dried to preserve natural sweetness. No added sugar, preservatives, or artificial colors — just pure fruit goodness in every bite.',
                'images' => [
                    'https://images.unsplash.com/photo-1606313564200-e75d5e304d0e?w=900&q=80',
                    'https://images.unsplash.com/photo-1608797178974-15b35a8edeaa?w=900&q=80',
                    'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=900&q=80',
                    'https://images.unsplash.com/photo-1599599810699-26642f847449?w=900&q=80',
                ],
                'description' => [
                    'Our Premium Dried Mango Slices are crafted from hand-selected Alphonso mangoes grown in the fertile valleys of Nepal. Each slice is carefully peeled, sliced, and slow-dried at low temperatures to lock in flavor, color, and nutrients.',
                    'Perfect as a guilt-free snack, trail mix addition, or topping for yogurt and desserts. Each 200g pack is resealable to keep your mangoes fresh and chewy.',
                ],
                'bullets' => [
                    '100% natural — no added sugar or preservatives',
                    'Rich in vitamins A & C and dietary fiber',
                    'Resealable stand-up pouch (200g net weight)',
                    'Ideal for snacking, baking, and gifting',
                ],
                'additionalInfo' => [
                    ['label' => 'Weight', 'value' => '200g'],
                    ['label' => 'Ingredients', 'value' => '100% dried mango'],
                    ['label' => 'Shelf Life', 'value' => '12 months (unopened)'],
                    ['label' => 'Storage', 'value' => 'Cool, dry place. Refrigerate after opening.'],
                    ['label' => 'Origin', 'value' => 'Nepal'],
                ],
            ],
            'organic-dried-banana-chips' => [
                'id' => 'organic-dried-banana-chips',
                'slug' => 'organic-dried-banana-chips',
                'name' => 'Organic Dried Banana Chips',
                'category' => 'Dried Fruit',
                'price' => 320,
                'priceFormatted' => 'Rs. 320',
                'comparePrice' => null,
                'comparePriceFormatted' => null,
                'discountPercent' => null,
                'inStock' => false,
                'onSale' => false,
                'rating' => 4,
                'reviewCount' => 18,
                'shortDescription' => 'Crispy organic banana chips with a naturally sweet crunch. Perfect for kids and adults alike.',
                'images' => [
                    'https://images.unsplash.com/photo-1571771894821-ce9b6d11d08e?w=900&q=80',
                    'https://images.unsplash.com/photo-1606313564200-e75d5e304d0e?w=900&q=80',
                ],
                'description' => [
                    'Lightly crisp banana chips made from organically grown bananas. A wholesome snack with no artificial flavors.',
                ],
                'bullets' => [
                    'Certified organic',
                    'No added sugar',
                    '150g resealable pack',
                ],
                'additionalInfo' => [
                    ['label' => 'Weight', 'value' => '150g'],
                    ['label' => 'Ingredients', 'value' => 'Organic banana'],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function relatedProducts(string $excludeId): array
    {
        $all = [
            [
                'id' => 'mixed-berry-medley',
                'image' => 'https://images.unsplash.com/photo-1615485925511-ef4e2b6d4e6e?w=400&q=80',
                'alt' => 'Mixed Berries',
                'name' => 'Mixed Berry Medley Pack',
                'price' => 'Rs. 680',
                'comparePrice' => 'Rs. 750',
                'rating' => 5,
                'reviews' => 31,
                'href' => url('/products/premium-dried-mango-slices'),
            ],
            [
                'id' => 'sun-dried-kiwi',
                'image' => 'https://images.unsplash.com/photo-1599599810699-26642f847449?w=400&q=80',
                'alt' => 'Dried Kiwi',
                'name' => 'Sun-Dried Kiwi Slices',
                'price' => 'Rs. 520',
                'rating' => 4.5,
                'reviews' => 12,
                'href' => url('/products/premium-dried-mango-slices'),
            ],
            [
                'id' => 'premium-dried-anjeer',
                'image' => 'https://images.unsplash.com/photo-1599599810769-0a29d5affa8e?w=400&q=80',
                'alt' => 'Anjeer',
                'name' => 'Premium Dried Anjeer (Fig)',
                'price' => 'Rs. 890',
                'rating' => 5,
                'reviews' => 42,
                'href' => url('/products/premium-dried-mango-slices'),
            ],
            [
                'id' => 'golden-dried-pineapple',
                'image' => 'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=400&q=80',
                'alt' => 'Dried Pineapple',
                'name' => 'Golden Dried Pineapple Rings',
                'price' => 'Rs. 410',
                'rating' => 5,
                'reviews' => 27,
                'href' => url('/products/premium-dried-mango-slices'),
            ],
        ];

        return array_values(array_filter($all, fn ($p) => $p['id'] !== $excludeId));
    }
}
