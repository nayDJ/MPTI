<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CustomerManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_customer_index_shows_page(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/customers')
                ->waitForText('Pelanggan')
                ->assertSee('Pelanggan');
        });
    }

    public function test_admin_can_create_new_customer(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/customers')
                ->waitForText('Pelanggan')
                ->press('Tambah Pelanggan')
                ->waitForInput('name')
                ->type('name', 'Budi Santoso')
                ->type('phone', '08123456789')
                ->type('address', 'Jl. Merdeka No. 10')
                ->press('Simpan')
                ->waitForText('Budi Santoso')
                ->assertSee('Budi Santoso');
        });
    }
}
