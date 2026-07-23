<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
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
        $response = $this->actingAs($this->user)->get(route('sales.index'));
        $response->assertOk();
    }

    public function test_store_creates_sale_with_items(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock' => 50, 'price' => 10000]);

        $response = $this->actingAs($this->user)->post(route('sales.store'), [
            'customer_id' => $customer->id,
            'sales_date' => now()->toDateString(),
            'payment_status' => 'lunas',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sales', ['customer_id' => $customer->id, 'payment_status' => 'lunas']);
        $this->assertDatabaseHas('sale_items', ['product_id' => $product->id, 'quantity' => 2]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('sales.store'), []);
        $response->assertSessionHasErrors(['customer_id', 'sales_date', 'items']);
    }

    public function test_store_fails_when_stock_insufficient(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock' => 1, 'track_stock' => true, 'price' => 10000]);

        $response = $this->actingAs($this->user)->from(route('sales.index'))->post(route('sales.store'), [
            'customer_id' => $customer->id,
            'sales_date' => now()->toDateString(),
            'payment_status' => 'lunas',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ]);

        $response->assertRedirect(route('sales.index'));
        $response->assertSessionHasErrors();
    }

    public function test_show_displays_sale(): void
    {
        $sale = Sale::factory()->create();
        $response = $this->actingAs($this->user)->get(route('sales.show', $sale));
        $response->assertOk();
    }

    public function test_update_payment_status(): void
    {
        $sale = Sale::factory()->belum()->create(['total_price' => 100000]);
        $response = $this->actingAs($this->user)->put(route('sales.update', $sale), [
            'payment_status' => 'lunas',
        ]);

        $response->assertRedirect(route('sales.index'));
        $this->assertEquals('lunas', $sale->fresh()->payment_status);
        $this->assertEquals(100000, (int) $sale->fresh()->paid_amount);
    }

    public function test_destroy_deletes_sale_and_items(): void
    {
        $sale = Sale::factory()->create();
        $sale->items()->create(['product_id' => Product::factory()->create()->id, 'quantity' => 1, 'subtotal' => 10000]);

        $response = $this->actingAs($this->user)->delete(route('sales.destroy', $sale));
        $response->assertRedirect(route('sales.index'));
        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
        $this->assertDatabaseMissing('sale_items', ['sales_id' => $sale->id]);
    }
}
