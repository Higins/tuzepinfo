<?php

namespace App\Jobs;

use App\Models\PriceHistory;
use App\Models\PriceSource;
use App\Services\PriceCollector\PriceCollectorService;
use App\Services\RabbitMQ\RabbitMQService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CollectPricesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private PriceSource $source;

    private RabbitMQService $rabbitMQ;

    private PriceCollectorService $collectorService;

    public function __construct(PriceSource $source)
    {
        $this->source = $source;
        $this->rabbitMQ = new RabbitMQService();
        $this->collectorService = new PriceCollectorService();
    }

    public function handle(): void
    {
        try {
            $this->collectorService->addCollector(new \App\Services\PriceCollector\WebScraperCollector());

            $collector = $this->findCollector($this->source);

            if (! $collector) {
                throw new \RuntimeException("No collector found for source type: {$this->source->type}");
            }

            $prices = $collector->collect($this->source);

            foreach ($prices as $priceData) {
                $product = $this->findOrCreateProduct($priceData);

                PriceHistory::create([
                    'product_id' => $product->id,
                    'price_source_id' => $this->source->id,
                    'price' => $priceData['price'],
                    'currency' => $priceData['currency'] ?? 'HUF',
                    'collected_at' => now(),
                    'metadata' => array_diff_key($priceData, ['price' => '', 'currency' => '']),
                ]);
            }

            $this->source->update(['last_sync_at' => now()]);

            $this->rabbitMQ->publish([
                'type' => 'price_collection_success',
                'source_id' => $this->source->id,
                'timestamp' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to collect prices from source {$this->source->id}: {$e->getMessage()}");

            $this->rabbitMQ->publish([
                'type' => 'price_collection_error',
                'source_id' => $this->source->id,
                'error' => $e->getMessage(),
                'timestamp' => now(),
            ]);

            throw $e;
        } finally {
            $this->rabbitMQ->close();
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Job failed for source {$this->source->id}: {$exception->getMessage()}");
    }

    private function findCollector(PriceSource $source): ?\App\Services\PriceCollector\PriceCollectorInterface
    {
        foreach ($this->collectorService->getCollectors() as $collector) {
            if ($collector->canHandle($source)) {
                return $collector;
            }
        }

        return null;
    }

    private function findOrCreateProduct(array $priceData): \App\Models\Product
    {
        return \App\Models\Product::firstOrCreate(
            ['external_id' => $priceData['external_id']],
            [
                'name' => $priceData['name'],
                'description' => $priceData['description'] ?? null,
                'category' => $priceData['category'] ?? null,
                'unit' => $priceData['unit'] ?? 'piece',
                'metadata' => array_diff_key($priceData, ['external_id' => '', 'name' => '', 'description' => '', 'category' => '', 'unit' => '']),
            ]
        );
    }
}
