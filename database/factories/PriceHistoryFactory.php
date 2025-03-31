<?php

namespace Database\Factories;

use App\Models\PriceHistory;
use App\Models\PriceSource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceHistoryFactory extends Factory
{
    protected $model = PriceHistory::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'price_source_id' => PriceSource::factory(),
            'price' => $this->faker->randomFloat(2, 100, 10000),
            'currency' => 'HUF',
            'collected_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'metadata' => [
                'vat_rate' => 27,
                'stock' => $this->faker->numberBetween(0, 100),
            ],
        ];
    }
}
