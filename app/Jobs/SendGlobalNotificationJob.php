<?php

namespace App\Jobs;

use App\Services\NotificationService;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendGlobalNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $message;

    public function __construct($title, $message)
    {
        $this->title = $title;
        $this->message = $message;
    }

    public function handle(NotificationService $service)
    {
        User::chunk(500, function ($users) use ($service) {
            foreach ($users as $user) {
                $service->storeNotifications($user->id, [
                    'title' => $this->title,
                    'message' => $this->message
                ]);
            }
        });
    }
}