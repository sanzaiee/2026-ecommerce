<?php

namespace Database\Seeders;

use App\Domain\Brand\Models\Brand;
use App\Domain\Category\Models\Category;
use App\Domain\CMS\Models\AboutPage;
use App\Domain\CMS\Models\LandingPage;
use App\Domain\Product\Models\Product;
use App\Domain\Review\Models\Review;
use App\Enums\LandingProductSection;
use App\Enums\ReviewStatus;
use App\Enums\StockStatus;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $driedFruits = Category::updateOrCreate(
            ['slug' => 'dried-fruits'],
            [
                'name' => 'Dried Fruits',
                'description' => 'Premium naturally dried fruits.',
                'sort_order' => 1,
            ]
        );

        $pickles = Category::updateOrCreate(
            ['slug' => 'pickles'],
            [
                'name' => 'Pickles',
                'description' => 'Traditional Nepali pickles.',
                'sort_order' => 2,
            ]
        );

        $mandira = Brand::updateOrCreate(
            ['slug' => 'mandira'],
            [
                'name' => 'Mandira',
                'description' => 'House brand — premium dried fruits and pickles from Nepal.',
            ]
        );

        $himalayanHarvest = Brand::updateOrCreate(
            ['slug' => 'himalayan-harvest'],
            [
                'name' => 'Himalayan Harvest',
                'description' => 'Curated Himalayan ingredients and trail mixes.',
            ]
        );

        $products = [
            [
                'title' => 'Premium Dried Mango Slices',
                'slug' => 'premium-dried-mango-slices',
                'short_description' => 'Sun-ripened Alphonso mangoes, slow-dried to preserve natural sweetness.',
                'description' => "Our Premium Dried Mango Slices are crafted from hand-selected Alphonso mangoes.\n\nPerfect as a guilt-free snack.",
                'price' => 450,
                'old_price' => 560,
                'stock_status' => StockStatus::Sale,
                'category_id' => $driedFruits->id,
                'brand_id' => $mandira->id,
                'rating_avg' => 0,
                'review_count' => 0,
                'bullets' => ['100% natural', 'Rich in vitamins A & C', '200g resealable pouch'],
                'additional_info' => [
                    ['label' => 'Weight', 'value' => '200g'],
                    ['label' => 'Origin', 'value' => 'Nepal'],
                ],
            ],
            [
                'title' => 'Organic Dried Banana Chips',
                'slug' => 'organic-dried-banana-chips',
                'short_description' => 'Crispy organic banana chips with a naturally sweet crunch.',
                'description' => 'Lightly crisp banana chips made from organically grown bananas.',
                'price' => 320,
                'stock_status' => StockStatus::OutOfStock,
                'category_id' => $driedFruits->id,
                'brand_id' => $himalayanHarvest->id,
                'rating_avg' => 0,
                'review_count' => 0,
            ],
            [
                'title' => 'Mixed Berry Medley Pack',
                'slug' => 'mixed-berry-medley',
                'short_description' => 'A vibrant blend of dried berries.',
                'price' => 680,
                'old_price' => 750,
                'stock_status' => StockStatus::Sale,
                'category_id' => $driedFruits->id,
                'brand_id' => $mandira->id,
                'rating_avg' => 0,
                'review_count' => 0,
            ],
            [
                'title' => 'Sun-Dried Kiwi Slices',
                'slug' => 'sun-dried-kiwi',
                'price' => 520,
                'stock_status' => StockStatus::InStock,
                'category_id' => $driedFruits->id,
                'brand_id' => $himalayanHarvest->id,
                'rating_avg' => 0,
                'review_count' => 0,
            ],
            [
                'title' => 'Himalayan Trail Mix 250g',
                'slug' => 'himalayan-trail-mix',
                'price' => 395,
                'stock_status' => StockStatus::InStock,
                'category_id' => $driedFruits->id,
                'brand_id' => $himalayanHarvest->id,
                'rating_avg' => 0,
                'review_count' => 0,
            ],
            [
                'title' => 'Golden Dried Pineapple Rings',
                'slug' => 'golden-dried-pineapple',
                'price' => 410,
                'old_price' => 480,
                'stock_status' => StockStatus::Sale,
                'category_id' => $driedFruits->id,
                'brand_id' => $mandira->id,
                'rating_avg' => 0,
                'review_count' => 0,
            ],
            [
                'title' => 'Turkish Dried Apricots',
                'slug' => 'turkish-dried-apricots',
                'price' => 580,
                'stock_status' => StockStatus::OutOfStock,
                'category_id' => $driedFruits->id,
                'brand_id' => $mandira->id,
                'rating_avg' => 0,
                'review_count' => 0,
            ],
            [
                'title' => 'Toasted Coconut Chips',
                'slug' => 'toasted-coconut-chips',
                'price' => 290,
                'stock_status' => StockStatus::InStock,
                'category_id' => $driedFruits->id,
                'rating_avg' => 0,
                'review_count' => 0,
            ],
            [
                'title' => 'Premium Dried Anjeer (Fig)',
                'slug' => 'premium-dried-anjeer',
                'price' => 890,
                'stock_status' => StockStatus::InStock,
                'category_id' => $driedFruits->id,
                'brand_id' => $himalayanHarvest->id,
                'rating_avg' => 0,
                'review_count' => 0,
            ],
            [
                'title' => 'Traditional Mango Pickle',
                'slug' => 'traditional-mango-pickle',
                'price' => 350,
                'stock_status' => StockStatus::InStock,
                'category_id' => $pickles->id,
                'brand_id' => $mandira->id,
                'rating_avg' => 0,
                'review_count' => 0,
            ],
            [
                'title' => 'Spicy Lime Pickle',
                'slug' => 'lime-pickle',
                'price' => 280,
                'old_price' => 320,
                'stock_status' => StockStatus::Sale,
                'category_id' => $pickles->id,
                'brand_id' => $mandira->id,
                'rating_avg' => 0,
                'review_count' => 0,
            ],
            [
                'title' => 'Festive Dry Fruit Gift Box',
                'slug' => 'festive-gift-box',
                'price' => 2450,
                'old_price' => 2800,
                'stock_status' => StockStatus::Sale,
                'category_id' => $driedFruits->id,
                'brand_id' => $mandira->id,
                'rating_avg' => 0,
                'review_count' => 0,
            ],
        ];

        $created = [];
        foreach ($products as $data) {
            $slug = $data['slug'];
            $created[$slug] = Product::updateOrCreate(['slug' => $slug], $data);
        }

        $landing = LandingPage::query()->updateOrCreate(['id' => 1], [
            'hero_title' => 'Handmade Newar pottery from Thimi — for home, garden and ritual.',
            'hero_subtitle' => 'Terracotta planting pots, clay water vessels and everyday ware — shaped on the wheel in Madhyapur.',
            'meta_title' => 'JheeKuma Clay Arts | Handmade Pottery from Thimi, Nepal',
            'meta_description' => 'Shop handmade terracotta pots, gamala, and clay water filters from Thimi potters. Fired by hand in Madhyapur — shipped across Nepal.',
            'meta_keywords' => 'Thimi pottery, Nepal clay pots, Newar pottery, clay water filter Nepal, terracotta plant pots Kathmandu, Madhyapur pottery',
        ]);

        $landing->featuredCategories()->sync([
            $driedFruits->id => ['sort_order' => 0],
            $pickles->id => ['sort_order' => 1],
        ]);

        $productSlugs = array_column($products, 'slug');
        $everydaySlugs = array_slice($productSlugs, 0, 8);
        $topSlugs = [$productSlugs[8], $productSlugs[9], $productSlugs[11], $productSlugs[3]];

        $pivots = [];
        foreach ($everydaySlugs as $i => $slug) {
            $pivots[$created[$slug]->id] = ['section' => LandingProductSection::Everyday->value, 'sort_order' => $i];
        }
        foreach ($topSlugs as $i => $slug) {
            $pivots[$created[$slug]->id] = ['section' => LandingProductSection::TopSelling->value, 'sort_order' => $i];
        }
        $landing->featuredProducts()->sync($pivots);

        AboutPage::query()->updateOrCreate(['id' => 1], [
            'hero_eyebrow' => 'Our story',
            'story_heading' => 'From Nepal to your table',
            'story_paragraph_1' => 'Our site brings premium dried fruits and traditional Nepali pickles to homes across the country. We partner with growers who share our respect for the land, then slow-dry, season, and pack each batch so flavor stays honest from the first bite to the last.',
            'story_paragraph_2' => 'Whether you are filling a pantry, packing a lunchbox, or sending a gift, we want every product to feel as thoughtful as something you would share with family.',
            'gallery_heading' => 'Crafted with care',
            'gallery_lead' => 'A glimpse of what we make — simple ingredients, proud Nepali roots.',
            'process_heading' => 'How we work',
            'values' => [
                ['icon' => 'bi-flower1', 'title' => '100% Natural', 'text' => 'No artificial preservatives, colors, or flavors in our core range.'],
                ['icon' => 'bi-droplet-half', 'title' => 'No Added Sugar', 'text' => 'Sun-ripened fruit, slow-dried to keep natural sweetness.'],
                ['icon' => 'bi-geo-alt', 'title' => 'Rooted in Nepal', 'text' => 'Trusted growers and time-honored methods from farm to pack.'],
                ['icon' => 'bi-box-seam', 'title' => 'Packed with Care', 'text' => 'Resealable pouches and careful handling for lasting freshness.'],
            ],
            'process_steps' => [
                ['title' => 'Select', 'text' => 'Hand-picked fruit and spices from partners we know and trust.'],
                ['title' => 'Prepare', 'text' => 'Slow drying and traditional recipes — no shortcuts on taste.'],
                ['title' => 'Pack', 'text' => 'Sealed for freshness and delivered across Nepal with care.'],
            ],
            'cta_title' => 'Taste the difference',
            'cta_text' => 'Explore our dried fruits, pickles, and gift-ready packs — shipped across Nepal.',
            'meta_title' => 'About Us — Our site',
            'meta_description' => 'Learn about Our site — premium dried fruits and traditional pickles crafted in Nepal.',
        ]);

        $sampleReviews = [
            ['slug' => 'premium-dried-mango-slices', 'name' => 'Priya S.', 'rating' => 5, 'comment' => 'Best dried mango I have tasted!'],
            ['slug' => 'mixed-berry-medley', 'name' => 'Anil K.', 'rating' => 5, 'comment' => 'Fresh berry flavor and perfect for gifting. Will order again.'],
            ['slug' => 'golden-dried-pineapple', 'name' => 'Sunita M.', 'rating' => 5, 'comment' => 'Sweet without being sugary. My kids love these as a school snack.'],
            ['slug' => 'himalayan-trail-mix', 'name' => 'Ramesh T.', 'rating' => 4, 'comment' => 'Great mix of textures. Arrived well packed and fresh.'],
        ];

        foreach ($sampleReviews as $review) {
            Review::updateOrCreate(
                [
                    'product_id' => $created[$review['slug']]->id,
                    'name' => $review['name'],
                ],
                [
                    'rating' => $review['rating'],
                    'comment' => $review['comment'],
                    'status' => ReviewStatus::Approved,
                ]
            );
        }

        $reviewedProductIds = collect($sampleReviews)
            ->pluck('slug')
            ->unique()
            ->map(fn (string $slug) => $created[$slug]->id);

        foreach ($reviewedProductIds as $productId) {
            $stats = Review::query()
                ->where('product_id', $productId)
                ->where('status', ReviewStatus::Approved)
                ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
                ->first();

            Product::query()->whereKey($productId)->update([
                'rating_avg' => round((float) ($stats->avg_rating ?? 0), 2),
                'review_count' => (int) ($stats->total ?? 0),
            ]);
        }
    }
}
