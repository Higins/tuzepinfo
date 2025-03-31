<?php

namespace Tests\Unit\Models;

use App\Models\PriceHistory;
use App\Models\PriceSource;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_product()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'category' => 'Test Category',
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'category' => 'Test Category',
        ]);
    }

    #[Test]
    public function it_can_get_latest_price()
    {
        $product = Product::factory()->create();
        $source = PriceSource::factory()->create();

        PriceHistory::create([
            'product_id' => $product->id,
            'price_source_id' => $source->id,
            'price' => 100,
            'currency' => 'HUF',
            'collected_at' => now()->subDays(2),
        ]);

        $latestPrice = PriceHistory::create([
            'product_id' => $product->id,
            'price_source_id' => $source->id,
            'price' => 200,
            'currency' => 'HUF',
            'collected_at' => now(),
        ]);

        $this->assertEquals($latestPrice->price, $product->latestPrice->price);
        $this->assertEquals($latestPrice->collected_at, $product->latestPrice->collected_at);
    }

    #[Test]
    public function it_can_get_price_history()
    {
        $product = Product::factory()->create();
        $source = PriceSource::factory()->create();

        $priceHistory = PriceHistory::create([
            'product_id' => $product->id,
            'price_source_id' => $source->id,
            'price' => 100,
            'currency' => 'HUF',
            'collected_at' => now(),
        ]);

        $this->assertCount(1, $product->priceHistory);
        $this->assertInstanceOf(PriceHistory::class, $product->priceHistory->first());
        $this->assertEquals($priceHistory->id, $product->priceHistory->first()->id);
    }
}
