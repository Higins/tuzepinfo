<?php

namespace Database\Seeders;

use App\Models\PriceHistory;
use App\Models\PriceSource;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PriceHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $sources = PriceSource::all();

        foreach ($products as $product) {
            foreach ($sources as $source) {
                // Minden termékhez minden forrásból létrehozunk 5 ár előzményt
                for ($i = 0; $i < 5; $i++) {
                    PriceHistory::create([
                        'product_id' => $product->id,
                        'price_source_id' => $source->id,
                        'price' => rand(100, 10000), // Véletlenszerű ár 100-10000 között
                        'currency' => 'HUF',
                        'collected_at' => now()->subDays($i), // Az utolsó 5 nap ár előzményei
                    ]);
                }
            }
        }
    }
}
