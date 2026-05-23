<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('landing', [
            'cartTotal' => 'Rs. 1,130',
            'hero' => [
                'title' => 'Mandira Premium Dried Fruits',
                'subtitle' => 'All natural. No added sugar, color.',
                'image' => 'https://images.unsplash.com/photo-1608797178974-15b35a8edeaa?w=800&q=80',
                'imageAlt' => 'Mixed dried fruits assortment',
            ],
            'categories' => [
                [
                    'title' => 'Dried Fruits',
                    'image' => 'https://images.unsplash.com/photo-1599599810769-0a29d5affa8e?w=700&q=80',
                    'href' => url('/shop') . '?category=dried-fruits',
                ],
                [
                    'title' => 'Pickles',
                    'image' => 'https://images.unsplash.com/photo-1625944525533-473f1a3d54e7?w=700&q=80',
                    'href' => url('/shop') . '?category=pickles',
                ],
            ],
            'everydayProducts' => $this->everydayProducts(),
            'topSellingProducts' => $this->topSellingProducts(),
            'reels' => $this->reels(),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function everydayProducts(): array
    {
        return [
            [
                'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e304d0e?w=400&q=80',
                'alt' => 'Dried Mango',
                'name' => 'Premium Dried Mango Slices',
                'price' => 'Rs. 450',
                'rating' => 4.5,
                'reviews' => 24,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1571771894821-ce9b6d11d08e?w=400&q=80',
                'alt' => 'Dried Banana',
                'name' => 'Organic Dried Banana Chips',
                'price' => 'Rs. 320',
                'rating' => 4,
                'reviews' => 18,
                'outOfStock' => true,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1615485925511-ef4e2b6d4e6e?w=400&q=80',
                'alt' => 'Mixed Berries',
                'name' => 'Mixed Berry Medley Pack',
                'price' => 'Rs. 680',
                'comparePrice' => 'Rs. 750',
                'rating' => 5,
                'reviews' => 31,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1599599810699-26642f847449?w=400&q=80',
                'alt' => 'Dried Kiwi',
                'name' => 'Sun-Dried Kiwi Slices',
                'price' => 'Rs. 520',
                'rating' => 4.5,
                'reviews' => 12,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1608797178974-15b35a8edeaa?w=400&q=80',
                'alt' => 'Trail Mix',
                'name' => 'Himalayan Trail Mix 250g',
                'price' => 'Rs. 395',
                'rating' => 4,
                'reviews' => 9,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=400&q=80',
                'alt' => 'Dried Pineapple',
                'name' => 'Golden Dried Pineapple Rings',
                'price' => 'Rs. 410',
                'rating' => 5,
                'reviews' => 27,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1603048297172-c92544798d5a?w=400&q=80',
                'alt' => 'Dried Apricot',
                'name' => 'Turkish Dried Apricots',
                'price' => 'Rs. 580',
                'rating' => 4.5,
                'reviews' => 15,
                'outOfStock' => true,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1519996529931-28324d5a630e?w=400&q=80',
                'alt' => 'Dried Coconut',
                'name' => 'Toasted Coconut Chips',
                'price' => 'Rs. 290',
                'rating' => 4,
                'reviews' => 8,
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function topSellingProducts(): array
    {
        return [
            [
                'image' => 'https://images.unsplash.com/photo-1599599810769-0a29d5affa8e?w=400&q=80',
                'alt' => 'Anjeer',
                'name' => 'Premium Dried Anjeer (Fig)',
                'price' => 'Rs. 890',
                'rating' => 5,
                'reviews' => 42,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1625944525533-473f1a3d54e7?w=400&q=80',
                'alt' => 'Mango Pickle',
                'name' => 'Traditional Mango Pickle',
                'price' => 'Rs. 350',
                'rating' => 4.5,
                'reviews' => 36,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e304d0e?w=400&q=80',
                'alt' => 'Gift Box',
                'name' => 'Festive Dry Fruit Gift Box',
                'price' => 'Rs. 2,450',
                'comparePrice' => 'Rs. 2,800',
                'rating' => 5,
                'reviews' => 58,
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1571771894821-ce9b6d11d08e?w=400&q=80',
                'alt' => 'Dates',
                'name' => 'Medjool Dates Premium',
                'price' => 'Rs. 720',
                'rating' => 4,
                'reviews' => 21,
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function reels(): array
    {
        return [
            ['src' => 'https://images.unsplash.com/photo-1608797178974-15b35a8edeaa?w=300&q=80', 'alt' => 'Reel 1'],
            ['src' => 'https://images.unsplash.com/photo-1599599810769-0a29d5affa8e?w=300&q=80', 'alt' => 'Reel 2'],
            ['src' => 'https://images.unsplash.com/photo-1606313564200-e75d5e304d0e?w=300&q=80', 'alt' => 'Reel 3'],
            ['src' => 'https://images.unsplash.com/photo-1625944525533-473f1a3d54e7?w=300&q=80', 'alt' => 'Reel 4'],
            ['src' => 'https://images.unsplash.com/photo-1571771894821-ce9b6d11d08e?w=300&q=80', 'alt' => 'Reel 5'],
            ['src' => 'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=300&q=80', 'alt' => 'Reel 6'],
            ['src' => 'https://images.unsplash.com/photo-1615485925511-ef4e2b6d4e6e?w=300&q=80', 'alt' => 'Reel 7'],
        ];
    }
}
