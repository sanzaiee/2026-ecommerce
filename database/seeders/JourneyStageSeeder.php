<?php

namespace Database\Seeders;

use App\Domain\Journey\Models\JourneyStage;
use App\Domain\Product\Models\Product;
use Illuminate\Database\Seeder;

class JourneyStageSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'title' => 'Clay',
                'text' => 'Fine river clay is collected, cleaned and wedged until it is ready for the wheel.',
                'sort_order' => 1,
            ],
            [
                'title' => 'Shape',
                'text' => 'The potter centers the clay and draws each form upward by hand on the traditional wheel.',
                'sort_order' => 2,
            ],
            [
                'title' => 'Dry',
                'text' => 'Vessels rest in the sun, slowly releasing moisture so they can survive the fire.',
                'sort_order' => 3,
            ],
            [
                'title' => 'Fire',
                'text' => 'An open kiln firing hardens the clay and gives each piece its earthen tone.',
                'sort_order' => 4,
            ],
            [
                'title' => 'Finish',
                'text' => 'Fired pieces are polished, checked and readied for kitchens and altars alike.',
                'sort_order' => 5,
            ],
        ];

        Product::query()->orderBy('id')->each(function (Product $product) use ($templates) {
            foreach ($templates as $stage) {
                JourneyStage::query()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'title' => $stage['title'],
                    ],
                    [
                        'text' => $stage['text'],
                        'sort_order' => $stage['sort_order'],
                        'is_published' => true,
                    ],
                );
            }
        });
    }
}
