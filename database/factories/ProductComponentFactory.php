<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductComponent>
 */
class ProductComponentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'component_product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(1, 5),
        ];
    }
}
