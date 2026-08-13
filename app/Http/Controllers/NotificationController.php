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
     * Dedicated Notifications Page
     */
    public function page(Request $request)
    {
        $user = $request->user();
        $filter = $request->query('filter', 'all');

        if ($filter === 'unread') {
            $query = $user->unreadNotifications();
        } elseif ($filter === 'read') {
            $query = $user->readNotifications();
        } else {
            $query = $user->notifications();
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();
        $readCount = $user->readNotifications()->count();

        return view('notifications.index', compact(
            'notifications',
            'unreadCount',
            'totalCount',
            'readCount',
            'filter'
        ));
    }

    /**
     * Render dropdown HTML via Blade partial (for bell.js fetch)
     */
    public function dropdown(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->limit(5)->get();
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

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
