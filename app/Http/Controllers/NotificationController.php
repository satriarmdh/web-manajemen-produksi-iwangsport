<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->limit(20)->get();
        $unreadCount = $request->user()->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Render dropdown HTML via Blade partial (for bell.js fetch)
     */
    public function dropdown(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->limit(20)->get();
        $unreadCount = $user->unreadNotifications()->count();

        return view('components.partials._notification-dropdown', compact('notifications', 'unreadCount'))->render();
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
