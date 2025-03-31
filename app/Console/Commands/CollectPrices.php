<?php

namespace App\Console\Commands;

use App\Models\PriceSource;
use App\Services\PriceCollector\PriceCollectorService;
use App\Services\PriceCollector\WebScraperCollector;
use Illuminate\Console\Command;

class CollectPrices extends Command
{
    protected $signature = 'prices:collect';

    protected $description = 'Collect prices from all active sources';

    public function handle(PriceCollectorService $collectorService): int
    {
        $this->info('Starting price collection...');

        $collectorService->addCollector(new WebScraperCollector());

        $sources = PriceSource::where('is_active', true)->get();
        $count = 0;

        foreach ($sources as $source) {
            try {
                $this->info("Collecting from source: {$source->name}");
                $collectorService->collectFromSource($source);
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to collect from {$source->name}: {$e->getMessage()}");
            }
        }

        $this->info("Completed! Successfully processed {$count} sources.");
        return true;
    }
}
