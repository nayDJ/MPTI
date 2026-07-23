<?php

namespace Tests\Browser;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SaleCreationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_sale_index_shows_page(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/sales')
                ->waitForText('Penjualan')
                ->assertSee('Penjualan');
        });
    }

    public function test_sale_show_page_loads(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['name' => 'Sale Customer']);
        $product = Product::factory()->create(['stock' => 10, 'track_stock' => true, 'price' => 15000]);
        $sale = \App\Models\Sale::factory()->create([
            'customer_id' => $customer->id,
            'total_price' => 30000,
            'payment_status' => 'lunas',
            'paid_amount' => 30000,
        ]);
        $sale->items()->create(['product_id' => $product->id, 'quantity' => 2, 'subtotal' => 30000]);

        $this->browse(function (Browser $browser) use ($user, $sale, $customer) {
            $browser->loginAs($user)
                ->visit('/sales/' . $sale->id)
                ->waitForText($customer->name)
                ->assertSee($customer->name);
        });
    }
}
