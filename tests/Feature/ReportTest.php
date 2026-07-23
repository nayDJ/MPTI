<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
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
        $response = $this->actingAs($this->user)->get(route('reports.index'));
        $response->assertOk();
    }

    public function test_index_accepts_period_filter(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.index', ['period' => 'bulan']));
        $response->assertOk();
    }

    public function test_index_accepts_view_filter(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.index', ['view' => 'income']));
        $response->assertOk();
    }
}
