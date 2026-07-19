<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $actionType = $request->action_type;

        $notifications = Notification::byType($actionType)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('notifications.index', compact('notifications', 'actionType'));
    }

    public function unread()
    {
        $notifications = Notification::latest()->take(5)->get();
        $unreadCount = Notification::where('is_read', false)->count();

        return response()->json([
            'data' => $notifications->map(fn($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'created_at' => $n->created_at->timezone('Asia/Jakarta')->format('g:i A'),
                'is_read' => $n->is_read,
            ]),
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead()
    {
        Notification::where('is_read', false)->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }
}
