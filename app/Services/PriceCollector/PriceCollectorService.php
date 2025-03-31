<?php

namespace App\Services\PriceCollector;

use App\Jobs\CollectPricesJob;
use App\Models\PriceSource;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class PriceCollectorService
{
    private array $collectors = [];

    public function __construct(array $collectors = [])
    {
        $this->collectors = $collectors;
    }

    public function addCollector(PriceCollectorInterface $collector): void
    {
        $this->collectors[] = $collector;
    }

    public function getCollectors(): array
    {
        return $this->collectors;
    }

    public function collectFromSource(PriceSource $source): void
    {
        CollectPricesJob::dispatch($source);
    }

    public function collectFromAllSources(): void
    {
        $sources = PriceSource::where('is_active', true)->get();

        foreach ($sources as $source) {
            try {
                $this->collectFromSource($source);
            } catch (\Exception $e) {
                Log::error("Failed to dispatch job for source {$source->id}: {$e->getMessage()}");
            }
        }
    }

    public function findCollector(PriceSource $source): ?PriceCollectorInterface
    {
        return $this->collectors[$source->type] ?? null;
    }

    private function findOrCreateProduct(array $data): Product
    {
        return Product::firstOrCreate(
            ['name' => $data['name']],
            [
                'category' => $data['category'],
                'unit' => $data['unit'],
            ]
        );
    }

    public function dispatchCollectionJob(PriceSource $source): void
    {
        CollectPricesJob::dispatch($source);
    }
}
