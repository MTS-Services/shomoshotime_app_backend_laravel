<?php

namespace App\Http\Controllers\API\V1;

use App\Events\GlobalNotificationEvent;
use App\Events\UserNotificationEvent;
use App\Http\Controllers\Controller;
use App\Jobs\SendGlobalNotificationJob;
use App\Models\PusherNotification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function getNotifications(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = $this->service->getUserNotifications($user->id);

        return sendResponse(true, 'User Notifications Fetched Successfully', $notifications, Response::HTTP_OK);
    }

    public function markAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:pusher_notifications,id',
            'is_read' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return sendResponse(false, $validator->errors()->first(), null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $data = $validator->validated();
        $notificationId = $data['id'];
        $isRead = $data['is_read'];
        $this->service->markAsRead($notificationId, $isRead);

        return sendResponse(true, 'Notification marked as read successfully', null, Response::HTTP_OK);

    }

    public function sendNotification(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
        }

        if (! $user->isAdmin()) {
            return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
        }
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|integer|exists:users,id',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return sendResponse(false, $validator->errors()->first(), null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();
        $title = $data['title'];
        $message = $data['message'];

        if (! empty($data['user_id'])) {
            $userId = $data['user_id'];

            $this->service->storeNotifications($userId, $data);

            event(new UserNotificationEvent($userId, $title, $message));

        } else {
            // 1. Fire Global Broadcast immediately (Fastest UI feedback)
            event(new GlobalNotificationEvent($title, $message));

            // 2. Dispatch Job to save to DB in background (Performance)
            SendGlobalNotificationJob::dispatch($title, $message);
        }

        return sendResponse(true, $title, $message, Response::HTTP_OK);
    }
}
