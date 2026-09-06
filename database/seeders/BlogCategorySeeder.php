<?php

namespace Database\Seeders;

use App\Domain\Blog\Models\BlogCategory;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Newari Food & Cuisine',
                'slug' => 'newari-food-cuisine',
                'description' => 'Yomari, bara, achar, feast-day spreads, and the flavors that define Newari kitchens.',
                'sort_order' => 70,
            ],
            [
                'name' => 'Livelihood & Crafts',
                'slug' => 'livelihood-crafts',
                'description' => 'Pottery, weaving, metalwork, and the handmade skills that sustain Newari households.',
                'sort_order' => 60,
            ],
            [
                'name' => 'Professions & Trade',
                'slug' => 'professions-trade',
                'description' => 'From market sellers and artisans to family-run shops — how Newars earn and exchange.',
                'sort_order' => 50,
            ],
            [
                'name' => 'Festivals & Rituals',
                'slug' => 'festivals-rituals',
                'description' => 'Indra Jatra, Sithi Nakha, Bisket Jatra, and the calendar that shapes community life.',
                'sort_order' => 40,
            ],
            [
                'name' => 'Culture & Heritage',
                'slug' => 'culture-heritage',
                'description' => 'Language, architecture, music, and customs passed down through Newar communities.',
                'sort_order' => 30,
            ],
            [
                'name' => 'Daily Life & Community',
                'slug' => 'daily-life-community',
                'description' => 'Neighborhood guthis, courtyards, markets, and the everyday rhythm of Newari living.',
                'sort_order' => 20,
            ],
            [
                'name' => 'Stories & Oral History',
                'slug' => 'stories-oral-history',
                'description' => 'Elders, memories, and personal accounts that keep Newari identity alive.',
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            BlogCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }

        BlogCategory::query()
            ->whereNotIn('slug', collect($categories)->pluck('slug'))
            ->delete();
    }
}
