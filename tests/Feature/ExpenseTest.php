<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
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
        $response = $this->actingAs($this->user)->get(route('expenses.index'));
        $response->assertOk();
    }

    public function test_store_creates_expense_and_redirects(): void
    {
        $response = $this->actingAs($this->user)->post(route('expenses.store'), [
            'description' => 'Beli kertas',
            'amount' => 50000,
            'category' => 'ATK',
            'expense_date' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('expenses.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('expenses', ['description' => 'Beli kertas', 'amount' => 50000]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('expenses.store'), []);
        $response->assertSessionHasErrors(['description', 'amount', 'category', 'expense_date']);
    }

    public function test_show_displays_expense(): void
    {
        $expense = Expense::factory()->create();
        $response = $this->actingAs($this->user)->get(route('expenses.show', $expense));
        $response->assertOk();
    }

    public function test_update_modifies_expense(): void
    {
        $expense = Expense::factory()->create(['description' => 'Old expense', 'amount' => 10000]);

        $response = $this->actingAs($this->user)->put(route('expenses.update', $expense), [
            'description' => 'Updated expense',
            'amount' => 20000,
            'category' => 'Transport',
            'expense_date' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', ['description' => 'Updated expense', 'amount' => 20000]);
    }

    public function test_export_pdf_returns_pdf(): void
    {
        $response = $this->actingAs($this->user)->get(route('expenses.export.pdf'));
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_destroy_deletes_expense(): void
    {
        $expense = Expense::factory()->create();
        $response = $this->actingAs($this->user)->delete(route('expenses.destroy', $expense));
        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }
}
