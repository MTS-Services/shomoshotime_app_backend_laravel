<?php

namespace App\Services;

use App\Models\PusherNotification;
use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class NotificationService
{
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
