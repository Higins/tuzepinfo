<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Tégla',
                'description' => 'Standard építőtégla',
                'unit' => 'db',
                'category' => 'Falazó anyagok',
            ],
            [
                'name' => 'Cement',
                'description' => 'Portland cement 25kg',
                'unit' => 'zsák',
                'category' => 'Kötőanyagok',
            ],
            [
                'name' => 'Homok',
                'description' => 'Építőhomok',
                'unit' => 'm³',
                'category' => 'Összetevők',
            ],
            [
                'name' => 'Vasbeton',
                'description' => 'Vasbeton elem',
                'unit' => 'db',
                'category' => 'Vasbeton elemek',
            ],
            [
                'name' => 'Csempe',
                'description' => 'Fali csempe',
                'unit' => 'm²',
                'category' => 'Burkolatok',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
