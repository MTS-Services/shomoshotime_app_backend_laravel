<?php

namespace App\Services\SMS;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DezSmsService
{
    protected string $apiUrl;
    protected string $senderId;
    protected string $authKey;
    protected string $dezsmsId;

    public function __construct()
    {
        $this->apiUrl = config('dezsms.api_url');
        $this->senderId = config('dezsms.sender_id');
        $this->authKey = config('dezsms.key');
        $this->dezsmsId = config('dezsms.dezsms_id');
    }

    /**
     * Send an SMS message via DezSMS.com gateway.
     *
     * @param string $mobile The recipient's mobile number.
     * @param string $message The SMS message content.
     * @return array An associative array with 'success' (boolean) and 'message' (string) or 'error' (string).
     */
    public function sendSms(string $mobile, string $message): array
    {
        // Basic validation for mobile number and message
        if (empty($mobile) || empty($message)) {
            return [
                'success' => false,
                'error' => 'Mobile number and message cannot be empty.'
            ];
        }

        $payload = [
            'msg' => $message,
            'number' => $mobile,
            'key' => $this->authKey,
            'dezsmsid' => $this->dezsmsId,
            'senderid' => $this->senderId,
        ];

        // Log the payload being sent for debugging
        Log::info('DezSMS API Request Payload:', $payload);

        try {
            $response = Http::asForm()->post($this->apiUrl, $payload);

            // Log the full response details for comprehensive debugging
            Log::info('DezSMS API Raw Response:', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers(),
            ]);

            $result = trim($response->body());

            // Check if the response indicates success
            if ($response->successful() && str_contains(strtolower($result), 'success')) {
                Log::info("SMS sent successfully to {$mobile}. Response: {$result}");
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully.'
                ];
            } else {
                // The API returned an error, capture and log all details
                Log::error("Failed to send SMS to {$mobile}. DezSMS Response: {$result}", [
                    'http_status' => $response->status(),
                    'error_body' => $response->body(),
                    'request_payload' => $payload,
                ]);
                return [
                    'success' => false,
                    'error' => 'DezSMS API Error (Status ' . $response->status() . '): ' . $result
                ];
            }
        } catch (Exception $e) {
            Log::error("Exception while sending SMS to {$mobile}: " . $e->getMessage(), [
                'exception_trace' => $e->getTraceAsString(),
                'request_payload' => $payload,
            ]);
            return [
                'success' => false,
                'error' => 'Network or API connection error: ' . $e->getMessage()
            ];
        }
    }
}
