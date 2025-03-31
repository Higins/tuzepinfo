<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'category' => $this->faker->word,
            'unit' => $this->faker->randomElement(['kg', 'db', 'l', 'm']),
            'description' => $this->faker->sentence,
            'metadata' => [
                'brand' => $this->faker->company,
                'sku' => $this->faker->unique()->numerify('SKU-####'),
            ],
        ];
    }
}
