<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class GlobalNotificationEvent implements ShouldBroadcast
{
    use SerializesModels;

    public $title;
    public $message;

    public function __construct($title, $message)
    {
        $this->title = $title;
        $this->message = $message;
    }

    // Broadcast on a public channel (or a shared private channel)
    public function broadcastOn()
    {
        return new Channel('global.notifications');
    }

    public function broadcastAs()
    {
        return 'global.notification';
    }

    public function broadcastWith()
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'is_global' => true,
        ];
    }
}