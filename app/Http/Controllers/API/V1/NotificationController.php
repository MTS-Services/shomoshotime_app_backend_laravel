<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Events\UserNotificationEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{

public function sendNotification(Request $request): JsonResponse
{
    $user = $request->user();

    if (! $user) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 401);
    }


    event(new UserNotificationEvent(
        $user->id,
        'You have a new notification'
    ));

    return response()->json([
        'message' => 'Notification sent successfully'
    ]);
}

}
