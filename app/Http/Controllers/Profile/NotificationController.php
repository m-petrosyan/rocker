<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController
{
    /**
     * Display a listing of notifications.
     */
        public function index(): Response
    {
        $user = auth()->user();

        // Mark all unread notifications as read
        $user->unreadNotifications->markAsRead();

        $notifications = $user
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Profile/Notifications/Index', [
            'notifications' => $notifications,
            'unreadCount' => 0, // Since we just marked them all as read
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    }
}
