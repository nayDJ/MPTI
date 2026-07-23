<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'total_price' => fake()->randomFloat(2, 10000, 500000),
            'sales_date' => fake()->dateTimeBetween('-90 days'),
            'payment_status' => fake()->randomElement(['belum', 'cicil', 'lunas']),
            'paid_amount' => 0,
        ];
    }

    public function lunas(): static
    {
        return $this->state(fn(array $attrs) => [
            'payment_status' => 'lunas',
            'paid_amount' => $attrs['total_price'] ?? 0,
        ]);
    }

    public function cicil(): static
    {
        return $this->state(fn(array $attrs) => [
            'payment_status' => 'cicil',
            'paid_amount' => round(($attrs['total_price'] ?? 0) * fake()->randomFloat(2, 0.1, 0.9), 2),
        ]);
    }

    public function belum(): static
    {
        return $this->state(fn() => [
            'payment_status' => 'belum',
            'paid_amount' => 0,
        ]);
    }
}
