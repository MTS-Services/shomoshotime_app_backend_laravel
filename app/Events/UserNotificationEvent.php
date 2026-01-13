<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserNotificationEvent implements ShouldBroadcast
{
    public $message;
    public $userId;

    public function __construct($userId, $message)
    {
        $this->userId = $userId;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'user.notification';
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->userId,
            'message' => $this->message,
        ];
    }
}
