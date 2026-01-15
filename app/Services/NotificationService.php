<?php

namespace App\Services;

use App\Models\PusherNotification;
use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class NotificationService
{
    public function getUserNotifications($userId)
    {
        return PusherNotification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markAsRead($notificationId, $isRead)
    {
        $notification = PusherNotification::where('id', $notificationId)->first();

        if ($notification) {
            $notification->is_read = $isRead;
            $notification->save();
        }

        return $notification;
    }
    public function storeNotifications($userId, $data)
    {
        $user = User::findOrFail($userId);

        if (!$user) {
            throw new \InvalidArgumentException("User not found");
        }

        return DB::transaction(function () use ($data, $userId) {
            return PusherNotification::create([
                'user_id' => $userId,
                'title' => $data['title'],
                'message' => $data['message'],
                'is_read' => 0,
                'created_by' => Auth::id()
            ]);

        });
    }
}
