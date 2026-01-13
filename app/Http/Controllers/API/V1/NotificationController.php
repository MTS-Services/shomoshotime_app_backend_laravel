<?php

namespace App\Http\Controllers\API\V1;

use App\Events\GlobalNotificationEvent;
use App\Events\UserNotificationEvent;
use App\Http\Controllers\Controller;
use App\Jobs\SendGlobalNotificationJob;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function sendNotification(Request $request): JsonResponse
    {
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
