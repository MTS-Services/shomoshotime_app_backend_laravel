<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $this->messaging = Firebase::messaging();
    }

    /**
     * Send push notification to a single device with duplicate prevention
     */
    public function sendToDevice(string $token, string $title, string $body, array $data = [])
    {
        try {
            // Create a unique key for this notification to prevent duplicates
            $notificationKey = md5($token . $title . $body . serialize($data));
            $cacheKey = "notification_sent_{$notificationKey}";
            
            // Check if this exact notification was sent recently (within 10 seconds)
            if (Cache::has($cacheKey)) {
                Log::info('Duplicate notification prevented', [
                    'token' => $token,
                    'title' => $title
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Notification already sent (duplicate prevented)',
                    'duplicate_prevented' => true
                ];
            }

            $notification = Notification::create($title, $body);

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification);

            if (!empty($data)) {
                $message = $message->withData($data);
            }
            
            $result = $this->messaging->send($message);

            // Cache this notification for 10 seconds to prevent duplicates
            Cache::put($cacheKey, true, 10);

            Log::info('Firebase notification sent successfully', [
                'token' => $token,
                'result' => $result
            ]);

            return [
                'success' => true,
                'message' => 'Notification sent successfully',
                'result' => $result
            ];
        } catch (Exception $e) {
            Log::error('Firebase notification failed', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send push notification to multiple devices
     */
    public function sendToMultipleDevices(array $tokens, string $title, string $body, array $data = [])
    {
        try {
            $notification = Notification::create($title, $body);

            $message = CloudMessage::new()
                ->withNotification($notification);

            if (!empty($data)) {
                $message = $message->withData($data);
            }

            $result = $this->messaging->sendMulticast($message, $tokens);

            Log::info('Firebase multicast notification sent', [
                'tokens_count' => count($tokens),
                'successful' => $result->successes()->count(),
                'failed' => $result->failures()->count()
            ]);

            return [
                'success' => true,
                'message' => 'Notifications processed',
                'successful' => $result->successes()->count(),
                'failed' => $result->failures()->count(),
                'results' => $result
            ];
        } catch (Exception $e) {
            Log::error('Firebase multicast notification failed', [
                'tokens_count' => count($tokens),
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send notifications: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to a topic
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = [])
    {
        try {
            $notification = Notification::create($title, $body);

            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification($notification);

            if (!empty($data)) {
                $message = $message->withData($data);
            }

            $result = $this->messaging->send($message);

            Log::info('Firebase topic notification sent successfully', [
                'topic' => $topic,
                'result' => $result
            ]);

            return [
                'success' => true,
                'message' => 'Topic notification sent successfully',
                'result' => $result
            ];
        } catch (Exception $e) {
            Log::error('Firebase topic notification failed', [
                'topic' => $topic,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send topic notification: ' . $e->getMessage()
            ];
        }
    }
}