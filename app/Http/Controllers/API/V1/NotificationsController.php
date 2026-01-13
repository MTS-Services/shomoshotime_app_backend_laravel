<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class NotificationsController extends Controller
{
    protected FirebaseNotificationService $notificationService;

    public function __construct(FirebaseNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Send notification to a single device
     */
    public function sendNotification(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'token' => 'required|string',
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:1000',
                'data' => 'nullable|array',
            ]);

            $result = $this->notificationService->sendToDevice(
                $validated['token'],
                $validated['title'],
                $validated['body'],
                $validated['data'] ?? []
            );

            return response()->json($result, $result['success'] ? 200 : 500);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultiple(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tokens' => 'required|array|min:1|max:500',
                'tokens.*' => 'required|string',
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:1000',
                'data' => 'nullable|array',
            ]);

            $result = $this->notificationService->sendToMultipleDevices(
                $validated['tokens'],
                $validated['title'],
                $validated['body'],
                $validated['data'] ?? []
            );

            return response()->json($result, $result['success'] ? 200 : 500);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to a topic
     */
    public function sendToTopic(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'topic' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:1000',
                'data' => 'nullable|array',
            ]);

            $result = $this->notificationService->sendToTopic(
                $validated['topic'],
                $validated['title'],
                $validated['body'],
                $validated['data'] ?? []
            );

            return response()->json($result, $result['success'] ? 200 : 500);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }
}