<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['id' => 7,  'name' => 'Galon 19L',              'category' => 'galon',     'stock' => 50,  'price' => 18000,  'low_stock_threshold' => 30, 'low_stock_alert_enabled' => true,  'is_active' => true],
            ['id' => 8,  'name' => 'Galon 15L',              'category' => 'galon',     'stock' => 30,  'price' => 15000,  'low_stock_threshold' => 30, 'low_stock_alert_enabled' => true,  'is_active' => true],
            ['id' => 9,  'name' => 'Air Tangki 5000L',       'category' => 'air_tanki', 'stock' => 5,   'price' => 150000, 'low_stock_threshold' => 3,  'low_stock_alert_enabled' => true,  'is_active' => true],
            ['id' => 10, 'name' => 'Air Tangki 8000L',       'category' => 'air_tanki', 'stock' => 3,   'price' => 220000, 'low_stock_threshold' => 3,  'low_stock_alert_enabled' => true,  'is_active' => true],
            ['id' => 11, 'name' => 'Botol 600ml (karton)',   'category' => 'kemasan',   'stock' => 20,  'price' => 35000,  'low_stock_threshold' => 10, 'low_stock_alert_enabled' => true,  'is_active' => true],
            ['id' => 12, 'name' => 'Botol 1500ml (karton)',  'category' => 'kemasan',   'stock' => 15,  'price' => 45000,  'low_stock_threshold' => 10, 'low_stock_alert_enabled' => true,  'is_active' => true],
            ['id' => 13, 'name' => 'Cup 240ml (karton)',     'category' => 'kemasan',   'stock' => 25,  'price' => 25000,  'low_stock_threshold' => 10, 'low_stock_alert_enabled' => true,  'is_active' => true],
            ['id' => 14, 'name' => 'Galon Kosong',           'category' => 'lainnya',   'stock' => 10,  'price' => 45000,  'low_stock_threshold' => 5,  'low_stock_alert_enabled' => false, 'is_active' => true],
            ['id' => 15, 'name' => 'Tutup Galon (pak)',      'category' => 'lainnya',   'stock' => 100, 'price' => 5000,   'low_stock_threshold' => 20, 'low_stock_alert_enabled' => false, 'is_active' => true],
            ['id' => 16, 'name' => 'Seal Plastik (pak)',     'category' => 'lainnya',   'stock' => 200, 'price' => 2000,   'low_stock_threshold' => 50, 'low_stock_alert_enabled' => false, 'is_active' => true],
        ];

        foreach ($products as $product) {
            $product['created_at'] = now();
            $product['updated_at'] = now();
            DB::table('products')->insert($product);
        }
    }
}
