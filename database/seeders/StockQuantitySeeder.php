<?php

namespace Database\Seeders;

use App\Domain\Product\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockQuantitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $stockSettings = [
            'premium-dried-mango-slices' => 25,
            'organic-dried-banana-chips' => 0,
            'mixed-berry-medley' => 15,
            'sun-dried-kiwi' => 30,
            'himalayan-trail-mix' => 45,
            'golden-dried-pineapple' => 8,
            'turkish-dried-apricots' => 0,
            'toasted-coconut-chips' => 3,
            'premium-dried-anjeer' => 50,
            'traditional-mango-pickle' => 20,
            'lime-pickle' => 12,
            'festive-gift-box' => 5,
        ];

        foreach ($stockSettings as $slug => $quantity) {
            $product = Product::where('slug', $slug)->first();
            
            if ($product) {
                $product->update(['stock_quantity' => $quantity]);
                
                $this->command->info("Updated stock for {$product->title}: {$quantity} units");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}