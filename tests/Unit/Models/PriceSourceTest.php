<?php

namespace Tests\Unit\Models;

use App\Models\PriceHistory;
use App\Models\PriceSource;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PriceSourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_price_source()
    {
        PriceSource::create([
            'name' => 'Test Source',
            'type' => 'web',
            'url' => 'https://example.com',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('price_sources', [
            'name' => 'Test Source',
            'type' => 'web',
            'url' => 'https://example.com',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function it_can_store_config_as_json()
    {
        $config = [
            'selector' => '.price',
            'currency' => 'HUF',
        ];

        $source = PriceSource::create([
            'name' => 'Test Source',
            'type' => 'web',
            'url' => 'https://example.com',
            'is_active' => true,
            'config' => $config,
        ]);

        $this->assertEquals($config, $source->config);
    }

    #[Test]
    public function it_can_get_price_history()
    {
        $source = PriceSource::factory()->create();
        $product = Product::factory()->create();

        $priceHistory = PriceHistory::create([
            'product_id' => $product->id,
            'price_source_id' => $source->id,
            'price' => 100,
            'currency' => 'HUF',
            'collected_at' => now(),
        ]);

        $this->assertCount(1, $source->priceHistory);
        $this->assertInstanceOf(PriceHistory::class, $source->priceHistory->first());
        $this->assertEquals($priceHistory->id, $source->priceHistory->first()->id);
    }

    #[Test]
    public function it_can_be_activated_and_deactivated()
    {
        $source = PriceSource::create([
            'name' => 'Test Source',
            'type' => 'web',
            'url' => 'https://example.com',
            'is_active' => true,
        ]);

        $this->assertTrue($source->is_active);

        $source->update(['is_active' => false]);
        $this->assertFalse($source->fresh()->is_active);

        $source->update(['is_active' => true]);
        $this->assertTrue($source->fresh()->is_active);
    }
}
