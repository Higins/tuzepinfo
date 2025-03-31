<?php

namespace Tests\Feature\Api;

use App\Models\PriceHistory;
use App\Models\PriceSource;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PriceControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_all_products_with_latest_prices()
    {
        $product1 = Product::factory()->create(['name' => 'Product 1']);
        $product2 = Product::factory()->create(['name' => 'Product 2']);
        $source = PriceSource::factory()->create();

        PriceHistory::factory()->create([
            'product_id' => $product1->id,
            'price_source_id' => $source->id,
            'price' => 100,
            'currency' => 'HUF',
            'collected_at' => now(),
        ]);

        PriceHistory::factory()->create([
            'product_id' => $product2->id,
            'price_source_id' => $source->id,
            'price' => 200,
            'currency' => 'HUF',
            'collected_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/prices');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'category',
                    'unit',
                    'latest_price' => [
                        'price',
                        'currency',
                        'source',
                        'updated_at',
                    ],
                ],
            ]);
    }

    #[Test]
    public function it_can_get_product_price_history()
    {
        $product = Product::factory()->create();
        $source = PriceSource::factory()->create();

        PriceHistory::factory()->create([
            'product_id' => $product->id,
            'price_source_id' => $source->id,
            'price' => 100,
            'currency' => 'HUF',
            'collected_at' => now()->subDays(2),
        ]);

        PriceHistory::factory()->create([
            'product_id' => $product->id,
            'price_source_id' => $source->id,
            'price' => 200,
            'currency' => 'HUF',
            'collected_at' => now(),
        ]);

        $response = $this->getJson("/api/v1/prices/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'category',
                'unit',
                'description',
                'price_history' => [
                    '*' => [
                        'price',
                        'currency',
                        'source',
                        'collected_at',
                    ],
                ],
            ])
            ->assertJsonCount(2, 'price_history');
    }

    #[Test]
    public function it_returns_404_for_non_existent_product()
    {
        $response = $this->getJson('/api/v1/prices/999');
        $response->assertStatus(404);
    }

    #[Test]
    public function it_returns_empty_price_history_for_product_without_prices()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/v1/prices/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'category',
                'unit',
                'description',
                'price_history',
            ])
            ->assertJsonCount(0, 'price_history');
    }
}
