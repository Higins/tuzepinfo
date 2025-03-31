<?php

namespace Database\Seeders;

use App\Models\PriceSource;
use Illuminate\Database\Seeder;

class PriceSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            [
                'name' => 'Tüzép.hu',
                'url' => 'https://tuzep.hu',
                'type' => 'web_scraper',
                'config' => json_encode([
                    'selector' => '.product-item',
                    'name_selector' => '.product-name',
                    'price_selector' => '.product-price',
                    'category_selector' => '.product-category',
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Építőanyag API',
                'url' => 'https://api.epitoanyag.hu',
                'type' => 'api',
                'config' => json_encode([
                    'api_key' => 'test_key',
                    'endpoint' => '/v1/products',
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Építőanyagok.hu',
                'url' => 'https://epitoanyagok.hu',
                'type' => 'web_scraper',
                'config' => json_encode([
                    'selector' => '.product-card',
                    'name_selector' => '.product-title',
                    'price_selector' => '.price',
                    'category_selector' => '.category',
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($sources as $source) {
            PriceSource::create($source);
        }
    }
}
