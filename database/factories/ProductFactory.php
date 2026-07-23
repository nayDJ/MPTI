<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'category' => fake()->word(),
            'stock' => fake()->numberBetween(0, 100),
            'price' => fake()->randomFloat(2, 1000, 100000),
            'low_stock_threshold' => fake()->numberBetween(5, 20),
            'low_stock_alert_enabled' => fake()->boolean(),
            'is_active' => true,
            'track_stock' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['is_active' => false]);
    }

    public function lowStock(): static
    {
        return $this->state(fn() => ['stock' => fake()->numberBetween(0, 5), 'low_stock_alert_enabled' => true]);
    }

    public function trackStockDisabled(): static
    {
        return $this->state(fn() => ['track_stock' => false]);
    }
}
