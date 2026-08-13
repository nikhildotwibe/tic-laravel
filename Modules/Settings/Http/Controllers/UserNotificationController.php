<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\UserNotification;
use Carbon\Carbon;

class UserNotificationController extends Controller
{
    /**
     * Get notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $userId = $request->user()?->id;

        if (!$userId) {
            return response()->json([
                'success' => true,
                'data' => [],
                'unread_count' => 0,
            ]);
        }

        $query = UserNotification::where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        $unreadCount = UserNotification::where('user_id', $userId)
            ->where(function($q) {
                $q->where('is_read', false)->orWhereNull('is_read');
            })
            ->count();

        $notifications = $query->limit(30)->get();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark single notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $userId = $request->user()?->id;

        $notification = UserNotification::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->update([
                'is_read' => true,
                'read_at' => Carbon::now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function markAllAsRead(Request $request)
    {
        $userId = $request->user()?->id;

        if ($userId) {
            UserNotification::where('user_id', $userId)
                ->where(function($q) {
                    $q->where('is_read', false)->orWhereNull('is_read');
                })
                ->update([
                    'is_read' => true,
                    'read_at' => Carbon::now(),
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }
}
