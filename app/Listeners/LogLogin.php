<?php

namespace App\Listeners;

use App\Models\Notification;

class LogLogin
{
    public function handle(object $event): void
    {
        Notification::create([
            'type' => 'info',
            'title' => 'Login',
            'message' => $event->user->name . ' login pada ' . now()->timezone('Asia/Jakarta')->isoFormat('D MMMM YYYY'),
            'action_type' => 'auth.login',
        ]);
    }
}
