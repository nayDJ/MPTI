<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    public function test_welcome_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertPathIs('/');
        });
    }
}
