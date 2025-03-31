<?php

namespace App\Services\PriceCollector;

use App\Models\PriceSource;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class WebScraperCollector implements PriceCollectorInterface
{
    public function collect(PriceSource $source): array
    {
        $response = Http::get($source->url);

        if (! $response->successful()) {
            throw new \RuntimeException("Failed to fetch data from {$source->url}");
        }

        $crawler = new Crawler($response->body());
        $config = $source->config;

        if ($crawler->filter($config['selector'])->count() === 0) {
            throw new \RuntimeException("No elements found matching selector: {$config['selector']}");
        }

        $prices = [];
        $crawler->filter($config['selector'])->each(function (Crawler $node) use (&$prices, $config) {
            if ($node->filter($config['name_selector'])->count() === 0) {
                throw new \RuntimeException("No elements found matching name selector: {$config['name_selector']}");
            }

            if ($node->filter($config['price_selector'])->count() === 0) {
                throw new \RuntimeException("No elements found matching price selector: {$config['price_selector']}");
            }

            $prices[] = [
                'name' => $node->filter($config['name_selector'])->text(),
                'price' => $this->extractPrice($node->filter($config['price_selector'])->text()),
                'category' => $node->filter($config['category_selector'] ?? '')->text(),
                'unit' => $config['unit'] ?? 'db',
            ];
        });

        if (empty($prices)) {
            throw new \RuntimeException('No prices found in the response');
        }

        return $prices;
    }

    public function canHandle(PriceSource $source): bool
    {
        return $source->type === 'web' && ! empty($source->url) && ! empty($source->config['selector']);
    }

    private function extractPrice(string $priceText): float
    {
        $price = preg_replace('/[^0-9.]/', '', $priceText);
        return (float) $price;
    }
}
