<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SaleCreateTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_create_sale_successfully(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['is_active' => true]);
        $product = Product::factory()->create([
            'price' => 10000,
            'stock' => 100,
            'track_stock' => true,
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) use ($user, $customer, $product) {
            $browser->loginAs($user)
                ->visit('/sales')
                ->waitForText('Riwayat Penjualan')
                ->press('Tambah Penjualan')
                ->waitForText('Input pesanan air ke sistem');

            $browser->script(
                "let d = Alpine.\$data(document.querySelector('form[x-data]'));
                d.customerId = {$customer->id};
                d.customerName = " . json_encode($customer->name) . ";
                d.customerSearch = " . json_encode($customer->name) . ";
                d.salesDate = " . json_encode(now()->format('Y-m-d')) . ";
                d.paymentStatus = 'lunas';
                d.items[0].product_id = {$product->id};
                d.items[0].product_name = " . json_encode($product->name) . ";
                d.items[0].product_price = {$product->price};
                d.items[0].product_stock = {$product->stock};
                d.items[0].quantity = 2;"
            );

            $browser->press('Lanjut ke Konfirmasi')
                ->waitForText('Konfirmasi Transaksi')
                ->press('Konfirmasi & Simpan')
                ->waitForText('Transaksi berhasil disimpan')
                ->assertSee('Transaksi berhasil disimpan');
        });
    }

    public function test_sale_validation_shows_errors(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/sales')
                ->waitForText('Riwayat Penjualan')
                ->press('Tambah Penjualan')
                ->waitForText('Input pesanan air ke sistem')
                ->press('Lanjut ke Konfirmasi')
                ->waitForText('Pilih customer')
                ->assertSee('Pilih customer');
        });
    }
}
