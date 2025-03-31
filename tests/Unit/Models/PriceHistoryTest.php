<?php

namespace Tests\Unit\Models;

use App\Models\PriceHistory;
use App\Models\PriceSource;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PriceHistoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_price_history()
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

        $this->assertDatabaseHas('price_history', [
            'product_id' => $product->id,
            'price_source_id' => $source->id,
            'price' => 100,
            'currency' => 'HUF',
        ]);
    }

    #[Test]
    public function it_belongs_to_a_product()
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

        $this->assertInstanceOf(Product::class, $priceHistory->product);
        $this->assertEquals($product->id, $priceHistory->product->id);
    }

    #[Test]
    public function it_belongs_to_a_price_source()
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

        $this->assertInstanceOf(PriceSource::class, $priceHistory->priceSource);
        $this->assertEquals($source->id, $priceHistory->priceSource->id);
    }

    #[Test]
    public function it_can_store_metadata()
    {
        $product = Product::factory()->create();
        $source = PriceSource::factory()->create();

        $metadata = [
            'url' => 'https://example.com',
            'raw_data' => ['some' => 'data'],
        ];

        $priceHistory = PriceHistory::create([
            'product_id' => $product->id,
            'price_source_id' => $source->id,
            'price' => 100,
            'currency' => 'HUF',
            'collected_at' => now(),
            'metadata' => $metadata,
        ]);

        $this->assertEquals($metadata, $priceHistory->metadata);
    }
}
