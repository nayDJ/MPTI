<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
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
        $response = $this->actingAs($this->user)->get(route('products.index'));
        $response->assertOk();
    }

    public function test_store_creates_product_and_redirects(): void
    {
        $response = $this->actingAs($this->user)->post(route('products.store'), [
            'name' => 'Test Product',
            'price' => 25000,
            'stock' => 10,
            'category' => 'Minuman',
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('products', ['name' => 'Test Product', 'price' => 25000]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('products.store'), []);
        $response->assertSessionHasErrors(['name', 'price']);
    }

    public function test_update_modifies_product(): void
    {
        $product = Product::factory()->create(['name' => 'Old Product', 'price' => 10000]);

        $response = $this->actingAs($this->user)->put(route('products.update', $product), [
            'name' => 'Updated Product',
            'price' => 20000,
            'stock' => 5,
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Updated Product', 'price' => 20000]);
    }

    public function test_destroy_deletes_product(): void
    {
        $product = Product::factory()->create();
        $response = $this->actingAs($this->user)->delete(route('products.destroy', $product));
        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_index_pagination(): void
    {
        Product::factory()->count(15)->create();

        $page1 = $this->actingAs($this->user)->get(route('products.index', ['page' => 1]));
        $page1->assertOk();

        $page2 = $this->actingAs($this->user)->get(route('products.index', ['page' => 2]));
        $page2->assertOk();
    }

    public function test_index_filters_by_category(): void
    {
        Product::factory()->create(['name' => 'Coffee', 'category' => 'Minuman']);
        Product::factory()->create(['name' => 'Tea', 'category' => 'Minuman']);
        Product::factory()->create(['name' => 'Nasi Goreng', 'category' => 'Makanan']);

        $response = $this->actingAs($this->user)->get(route('products.index', ['category' => 'Minuman']));
        $response->assertOk();
    }

    public function test_export_pdf_returns_pdf(): void
    {
        $response = $this->actingAs($this->user)->get(route('products.export.pdf'));
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_toggle_status_changes_active_state(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        $response = $this->actingAs($this->user)->post(route('products.toggle-status', $product));
        $response->assertRedirect();
        $this->assertEquals(0, $product->fresh()->is_active);
    }
}
