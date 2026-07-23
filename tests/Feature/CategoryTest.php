<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_category_and_returns_json(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Electronics',
        ]);

        $response->assertJson(['name' => 'Electronics']);
        $this->assertDatabaseHas('categories', ['name' => 'Electronics']);
    }

    public function test_store_validates_unique_name(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('categories.store'), ['name' => 'Duplicate']);
        $response = $this->actingAs($user)->post(route('categories.store'), ['name' => 'Duplicate']);

        $response->assertSessionHasErrors(['name']);
    }
}
