<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Fetch the latest unread notifications for the authenticated user (used in topbar).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function fetchTopbarNotifications()
    {
        return Notification::where('user_id', Auth::id())->where('read', false)->latest()->take(5)->get();
    }
    /**
     * Mark a specific notification as read for the authenticated user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $notification->update(['read' => true]);
        return response()->json(['status' => 'success']);
    }
}
