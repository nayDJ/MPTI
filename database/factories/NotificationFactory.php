<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement([Notification::TYPE_SUCCESS, Notification::TYPE_ERROR, Notification::TYPE_INFO]),
            'title' => fake()->sentence(3),
            'message' => fake()->paragraph(),
            'action_type' => fake()->randomElement([
                Notification::ACTION_SALE_CREATE,
                Notification::ACTION_PRODUCT_UPDATE,
                Notification::ACTION_CUSTOMER_DELETE,
                Notification::ACTION_EXPENSE_CREATE,
            ]),
            'is_read' => fake()->boolean(20),
        ];
    }

    public function unread(): static
    {
        return $this->state(fn() => ['is_read' => false]);
    }

    public function read(): static
    {
        return $this->state(fn() => ['is_read' => true]);
    }

    public function ofType(string $actionType): static
    {
        return $this->state(fn() => ['action_type' => $actionType]);
    }
}
