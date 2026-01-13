<?php

namespace App\Http\Controllers\API\V1;

use App\Events\UserNotificationEvent;
use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }
    
    public function sendNotification(Request $request): JsonResponse
    {
        $validation = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);        
        $data = $validation;
        $userId = $data['user_id'];
        $title = $data['title'];
        $message = $data['message'];
        $this->service->storeNotifications($userId,$data);
        event(new UserNotificationEvent($userId, $title, $message));

        return sendResponse(true, $title ,$message , Response::HTTP_OK);
    }
}
