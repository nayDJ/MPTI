<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_index_returns_view(): void
    {
        $response = $this->actingAs($this->user)->get(route('customers.index'));
        $response->assertOk();
    }

    public function test_store_creates_customer_and_redirects(): void
    {
        $response = $this->actingAs($this->user)->post(route('customers.store'), [
            'name' => 'John Doe',
            'phone' => '08123456789',
            'address' => 'Jl. Merdeka No.1',
        ]);

        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('customers', ['name' => 'John Doe']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('customers.store'), []);
        $response->assertSessionHasErrors(['name']);
    }

    public function test_show_displays_customer(): void
    {
        $customer = Customer::factory()->create();
        $response = $this->actingAs($this->user)->get(route('customers.show', $customer));
        $response->assertOk();
    }

    public function test_update_modifies_customer(): void
    {
        $customer = Customer::factory()->create(['name' => 'Old Name']);
        $response = $this->actingAs($this->user)->put(route('customers.update', $customer), [
            'name' => 'Updated Name',
            'phone' => '08987654321',
            'address' => 'Jl. Baru No.2',
        ]);

        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('customers', ['name' => 'Updated Name']);
    }

    public function test_destroy_deletes_customer(): void
    {
        $customer = Customer::factory()->create();
        $response = $this->actingAs($this->user)->delete(route('customers.destroy', $customer));
        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_toggle_status_activates_inactive_customer(): void
    {
        $customer = Customer::factory()->inactive()->create();
        $response = $this->actingAs($this->user)->post(route('customers.toggle-status', $customer));
        $response->assertRedirect();
        $this->assertEquals(1, $customer->fresh()->is_active);
    }

    public function test_quick_store_returns_json(): void
    {
        $response = $this->actingAs($this->user)->post(route('customers.quick-add'), [
            'name' => 'Quick Customer',
        ]);

        $response->assertJson(['name' => 'Quick Customer']);
        $this->assertDatabaseHas('customers', ['name' => 'Quick Customer']);
    }
}
