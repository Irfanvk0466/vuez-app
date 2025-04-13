<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Fetch latest unread notifications.
     */
    public function fetchTopbarNotifications()
    {
        return Notification::where('user_id', Auth::id())->where('read', false)->latest()->take(5)->get();
    }
    /**
     * Mark notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $notification->update(['read' => true]);
        return response()->json(['status' => 'success']);
    }
}
