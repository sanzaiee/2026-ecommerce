<?php

namespace Database\Seeders;

use App\Domain\Blog\Models\Blog;
use App\Domain\Blog\Models\BlogCategory;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = BlogCategory::query()->pluck('id', 'slug');

        $blogs = [
            [
                'title' => 'What Makes Newari Achar Different from Everyday Pickles',
                'slug' => 'newari-achar-differences',
                'blog_category_id' => $categories['newari-food-cuisine'] ?? null,
                'content' => '<p>Mustard oil, roasted spices, and patience — the trio behind achars that outlast the season.</p>',
                'excerpt' => 'How Newari pickle traditions use fermentation, spice blends, and sun to create achars with depth and longevity.',
                'position' => 8,
                'is_featured' => true,
            ],
            [
                'title' => 'Yomari: The Newari Dumpling That Marks Winter Festivals',
                'slug' => 'yomari-newari-winter-festival-food',
                'blog_category_id' => $categories['newari-food-cuisine'] ?? null,
                'content' => '<p>Folded rice flour, filled with chaku or lentil — a sweet and savory symbol of Yomari Punhi.</p>',
                'excerpt' => 'The story, shape, and seasonal meaning of yomari in Newari homes during Yomari Punhi.',
                'position' => 7,
                'is_featured' => true,
            ],
            [
                'title' => 'Potters of Bhaktapur: A Livelihood Shaped by Clay and Fire',
                'slug' => 'bhaktapur-potters-livelihood',
                'blog_category_id' => $categories['livelihood-crafts'] ?? null,
                'content' => '<p>At the pottery square, wheels turn the way they have for generations — each vessel tied to ritual and trade.</p>',
                'excerpt' => 'How Newari potters in Bhaktapur sustain a craft linked to festivals, kitchens, and local markets.',
                'position' => 6,
                'is_featured' => false,
            ],
            [
                'title' => 'From Haku Patashi to Shop Counter: Newari Trade in the Valley',
                'slug' => 'newari-trade-valley-shops',
                'blog_category_id' => $categories['professions-trade'] ?? null,
                'content' => '<p>Many Newari families moved from home-based work to storefronts without leaving their craft behind.</p>',
                'excerpt' => 'A look at how traditional Newari professions evolved into the shops and food businesses of Kathmandu Valley.',
                'position' => 5,
                'is_featured' => false,
            ],
            [
                'title' => 'Indra Jatra: What Happens in the Streets of Kathmandu',
                'slug' => 'indra-jatra-kathmandu-streets',
                'blog_category_id' => $categories['festivals-rituals'] ?? null,
                'content' => '<p>Masked dances, chariot processions, and living goddess appearances — the city pauses for eight days.</p>',
                'excerpt' => 'A guide to Indra Jatra rituals, processions, and the Newari communities that keep the festival alive.',
                'position' => 4,
                'is_featured' => false,
            ],
            [
                'title' => 'Newari Courtyards: Where Culture Is Lived, Not Displayed',
                'slug' => 'newari-courtyards-culture',
                'blog_category_id' => $categories['culture-heritage'] ?? null,
                'content' => '<p>Wood-carved windows, shared wells, and festival altars — the bahi and chuka still hold the neighborhood together.</p>',
                'excerpt' => 'Why Newari courtyard architecture remains central to art, worship, and community gathering.',
                'position' => 3,
                'is_featured' => false,
            ],
            [
                'title' => 'Morning at Asan: A Newari Market Routine',
                'slug' => 'asan-market-newari-daily-life',
                'blog_category_id' => $categories['daily-life-community'] ?? null,
                'content' => '<p>Before the city wakes fully, vendors stack vegetables, spices, and dried goods along narrow lanes.</p>',
                'excerpt' => 'Daily market life in Asan — where Newari households source ingredients for feasts and everyday meals.',
                'position' => 2,
                'is_featured' => false,
            ],
            [
                'title' => 'Grandmother\'s Pickle Jar: An Oral History of Family Recipes',
                'slug' => 'grandmother-pickle-jar-oral-history',
                'blog_category_id' => $categories['stories-oral-history'] ?? null,
                'content' => '<p>Every family jar carries a story — who taught whom, which festival it was opened for, and why the recipe never changed.</p>',
                'excerpt' => 'How oral tradition preserves Newari food knowledge when recipes are rarely written down.',
                'position' => 1,
                'is_featured' => false,
            ],
        ];

        foreach ($blogs as $blog) {
            Blog::updateOrCreate(
                ['slug' => $blog['slug']],
                $blog
            );
        }
    }
}
