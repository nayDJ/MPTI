<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_customers(): void
    {
        $this->get(route('customers.index'))->assertRedirect(route('login'));
        $this->get(route('customers.show', Customer::factory()->create()))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_products(): void
    {
        $this->get(route('products.index'))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_sales(): void
    {
        $this->get(route('sales.index'))->assertRedirect(route('login'));
        $this->get(route('sales.show', Sale::factory()->create()))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_expenses(): void
    {
        $this->get(route('expenses.index'))->assertRedirect(route('login'));
        $this->get(route('expenses.show', Expense::factory()->create()))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_reports(): void
    {
        $this->get(route('reports.index'))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_notifications(): void
    {
        $this->get(route('notifications.index'))->assertRedirect(route('login'));
    }
}
