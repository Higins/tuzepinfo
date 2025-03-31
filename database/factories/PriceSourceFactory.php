<?php

namespace Database\Factories;

use App\Models\PriceSource;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceSourceFactory extends Factory
{
    protected $model = PriceSource::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'type' => $this->faker->randomElement(['web', 'api', 'file']),
            'url' => $this->faker->url,
            'config' => [
                'selector' => $this->faker->word,
                'timeout' => $this->faker->numberBetween(5, 30),
            ],
            'is_active' => $this->faker->boolean(80),
            'last_sync_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
