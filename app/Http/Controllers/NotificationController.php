<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display notifications center
     */
    public function index()
    {
        return view('notifications.index');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead()
    {
        Notification::where('notifiable_id', auth()->id())
            ->where('notifiable_type', get_class(auth()->user()))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * Mark single notification as read
     */
    public function markRead(Notification $notification)
    {
        // Verify ownership
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }
}
