<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
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
        $response = $this->actingAs($this->user)->get(route('notifications.index'));
        $response->assertOk();
    }

    public function test_unread_returns_json(): void
    {
        Notification::factory()->unread()->count(3)->create();
        Notification::factory()->read()->count(2)->create();

        $response = $this->actingAs($this->user)->get(route('notifications.unread'));
        $response->assertJson(['unread_count' => 3]);
    }

    public function test_mark_as_read_updates_all_notifications(): void
    {
        Notification::factory()->unread()->count(3)->create();

        $this->actingAs($this->user)->post(route('notifications.mark-read'));

        $this->assertEquals(0, Notification::where('is_read', false)->count());
    }
}
