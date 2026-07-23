<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@nnqua.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->waitForInput('email')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Masuk')
                ->waitForLocation('/dashboard')
                ->assertPathIs('/dashboard');
        });
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'admin@nnqua.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitForInput('email')
                ->type('email', 'admin@nnqua.com')
                ->type('password', 'wrongpassword')
                ->press('Masuk')
                ->waitForText('These credentials do not match our records.')
                ->assertSee('These credentials do not match our records.');
        });
    }
}
