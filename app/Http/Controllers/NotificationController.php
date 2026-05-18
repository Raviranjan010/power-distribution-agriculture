<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications and count for polling.
     */
    public function poll(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'notifications' => []], 401);
        }

        $unreadCount = $user->unreadNotifications()->count();
        $notifications = $user->unreadNotifications()
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? 'Notification',
                    'message' => $notification->data['message'] ?? '',
                    'url' => $notification->data['url'] ?? '#',
                    'icon' => $notification->data['icon'] ?? 'fa-solid fa-bell',
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Mark a specific notification as read and redirect if url is present.
     */
    public function read($id)
    {
        $user = Auth::user();
        if ($user) {
            $notification = $user->notifications()->find($id);
            if ($notification) {
                $notification->markAsRead();
                $url = $notification->data['url'] ?? null;
                if ($url && $url !== '#') {
                    return redirect($url);
                }
            }
        }

        return back();
    }
}
