<?php

namespace Database\Factories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'description' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 1000, 500000),
            'category' => fake()->word(),
            'expense_date' => fake()->dateTimeBetween('-90 days'),
        ];
    }
}
