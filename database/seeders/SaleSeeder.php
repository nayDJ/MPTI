<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $products = Product::all();

        $weights = ['lunas', 'lunas', 'lunas', 'lunas', 'lunas', 'lunas', 'lunas', 'cicil', 'cicil', 'belum'];

        for ($i = 0; $i < 50; $i++) {
            $date = Carbon::now()->subDays(rand(1, 90));
            $status = $weights[array_rand($weights)];
            $customer = $customers->random();
            $itemCount = rand(1, 5);
            $items = [];
            $totalPrice = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(1, 10);
                $subtotal = (int) $product->price * $quantity;
                $totalPrice += $subtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ];
            }

            $paidAmount = match ($status) {
                'lunas' => $totalPrice,
                'cicil' => (int) ($totalPrice * rand(30, 80) / 100),
                default => 0,
            };

            $sale = Sale::create([
                'customer_id' => $customer->id,
                'total_price' => $totalPrice,
                'paid_amount' => $paidAmount,
                'payment_status' => $status,
                'sales_date' => $date->format('Y-m-d'),
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($items as $item) {
                SaleItem::create([
                    'sales_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }
        }
    }
}
