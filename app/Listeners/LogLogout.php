<?php

namespace App\Listeners;

use App\Models\Notification;

class LogLogout
{
    public function handle(object $event): void
    {
        Notification::create([
            'type' => 'info',
            'title' => 'Logout',
            'message' => $event->user->name . ' logout pada ' . now()->timezone('Asia/Jakarta')->isoFormat('D MMMM YYYY'),
            'action_type' => 'auth.logout',
        ]);
    }
}
