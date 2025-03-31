<?php

namespace Tests\Unit;

use App\Jobs\CollectPricesJob;
use App\Models\PriceSource;
use App\Services\PriceCollector\PriceCollectorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PriceCollectorTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_can_collect_prices_from_web_source()
    {
        Queue::fake();

        $source = PriceSource::factory()->create([
            'type' => 'web',
            'url' => 'https://example.com',
            'config' => [
                'selector' => '.price',
                'currency' => 'HUF',
            ],
        ]);

        $collector = new PriceCollectorService();
        $collector->collectFromSource($source);

        Queue::assertPushed(CollectPricesJob::class);
    }
}
