<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ToggleStatusTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_toggle_customer_status(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['is_active' => true]);

        $this->browse(function (Browser $browser) use ($user, $customer) {
            $browser->loginAs($user)
                ->visit('/customers')
                ->waitForText($customer->name)
                ->click("form[action*='{$customer->id}/toggle-status'] button[type='submit']")
                ->waitForText('Status customer berhasil diubah')
                ->assertSee('Status customer berhasil diubah');
        });
    }

    public function test_toggle_product_status(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);

        $this->browse(function (Browser $browser) use ($user, $product) {
            $browser->loginAs($user)
                ->visit('/products')
                ->waitForText($product->name)
                ->click("form[action*='{$product->id}/toggle-status'] button[type='submit']")
                ->waitForText('Status produk berhasil diperbarui')
                ->assertSee('Status produk berhasil diperbarui');
        });
    }

    public function test_toggle_customer_twice_returns_to_active(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['is_active' => true]);

        $this->browse(function (Browser $browser) use ($user, $customer) {
            $browser->loginAs($user)
                ->visit('/customers')
                ->waitForText($customer->name)
                ->click("form[action*='{$customer->id}/toggle-status'] button[type='submit']")
                ->waitForText('Status customer berhasil diubah')
                ->click("form[action*='{$customer->id}/toggle-status'] button[type='submit']")
                ->waitForText('Status customer berhasil diubah')
                ->assertSee('Status customer berhasil diubah');
        });
    }
}
