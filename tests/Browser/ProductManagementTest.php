<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProductManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_product_index_shows_page(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/products')
                ->waitForText('Produk')
                ->assertSee('Produk');
        });
    }

    public function test_admin_can_create_product(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/products')
                ->waitForText('Produk')
                ->press('Tambah Produk')
                ->waitForInput('name')
                ->type('name', 'Air Mineral 600ml')
                ->type('price', '5000')
                ->type('stock', '100')
                ->press('Simpan')
                ->waitForText('Air Mineral 600ml')
                ->assertSee('Air Mineral 600ml');
        });
    }
}
