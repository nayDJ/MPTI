<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductComponent;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_scope_collectable_revenue_returns_sum_for_lunas(): void
    {
        Sale::factory()->lunas()->create(['total_price' => 100000]);
        $result = Sale::collectableRevenue()->value('total');
        $this->assertEquals(100000, (int) $result);
    }

    public function test_sale_scope_collectable_revenue_returns_paid_amount_for_cicil(): void
    {
        Sale::factory()->cicil()->create(['total_price' => 100000, 'paid_amount' => 30000]);
        $result = Sale::collectableRevenue()->value('total');
        $this->assertEquals(30000, (int) $result);
    }

    public function test_sale_scope_collectable_revenue_returns_zero_for_belum(): void
    {
        Sale::factory()->belum()->create(['total_price' => 100000]);
        $result = Sale::collectableRevenue()->value('total');
        $this->assertEquals(0, (int) $result);
    }

    public function test_notification_scope_unread(): void
    {
        Notification::factory()->unread()->count(3)->create();
        Notification::factory()->read()->count(2)->create();

        $this->assertEquals(3, Notification::unread()->count());
    }

    public function test_notification_scope_by_type(): void
    {
        Notification::factory()->ofType(Notification::ACTION_SALE_CREATE)->count(2)->create();
        Notification::factory()->ofType(Notification::ACTION_PRODUCT_CREATE)->count(1)->create();

        $this->assertEquals(2, Notification::byType('sale')->count());
    }

    public function test_product_has_sale_items_relationship(): void
    {
        $product = Product::factory()->create();
        $sale = Sale::factory()->create();
        SaleItem::factory()->create(['product_id' => $product->id, 'sales_id' => $sale->id]);

        $this->assertCount(1, $product->saleItems);
    }

    public function test_customer_has_sales_relationship(): void
    {
        $customer = Customer::factory()->create();
        Sale::factory()->count(3)->create(['customer_id' => $customer->id]);

        $this->assertCount(3, $customer->sales);
    }

    public function test_product_component_auto_decrement(): void
    {
        $parent = Product::factory()->create(['stock' => 10, 'track_stock' => true]);
        $component = Product::factory()->create(['stock' => 20, 'track_stock' => true]);
        ProductComponent::factory()->create([
            'product_id' => $parent->id,
            'component_product_id' => $component->id,
            'quantity' => 2,
        ]);

        $this->assertCount(1, $parent->components);
        $this->assertEquals(20, $component->stock);
    }

    public function test_stock_unchanged_when_track_stock_disabled(): void
    {
        $product = Product::factory()->create(['stock' => 5, 'track_stock' => false]);

        $customer = Customer::factory()->create();
        $sale = Sale::factory()->create(['customer_id' => $customer->id]);
        $sale->items()->create(['product_id' => $product->id, 'quantity' => 3, 'subtotal' => 30000]);

        $this->assertEquals(5, $product->fresh()->stock);
    }

    public function test_sale_has_items_relationship(): void
    {
        $sale = Sale::factory()->create();
        $product = Product::factory()->create();
        SaleItem::factory()->count(2)->create(['sales_id' => $sale->id, 'product_id' => $product->id]);

        $this->assertCount(2, $sale->items);
    }
}
