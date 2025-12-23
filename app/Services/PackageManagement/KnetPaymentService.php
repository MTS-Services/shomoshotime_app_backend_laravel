<?php
namespace App\Services\PackageManagement;

use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class KnetPaymentService
{
    private $config;
    private $merchantId;
    private $merchantPassword;
    private $encryptedKey;
    private $gatewayUrl;
    private $returnUrl;

    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->config = config('knet');
        $this->merchantId = $this->config['merchant_id'];
        $this->merchantPassword = $this->config['merchant_password'];
        $this->encryptedKey = $this->config['encrypted_key'];
        $this->gatewayUrl = $this->config['gateway_url'] ?? 'https://www.kpay.com.kw/kpg/paymentpage.htm';
        $this->returnUrl = $this->config['return_url'];

        $this->paymentService = $paymentService;
    }

    /**
     * Initiate payment transaction
     */
    public function initiatePayment(array $paymentData, string $transactionId): array
    {
        try {
            $this->validatePaymentData($paymentData);

            // For K-Pay, try direct URL generation first (if no registration required)
            // Many payment gateways allow direct URL generation without pre-registration
            $paymentId = $this->generatePaymentId($paymentData, $transactionId);
            $paymentUrl = $this->generatePaymentUrl($paymentId);

            // Update payment record with PaymentID
            // $this->paymentService->updatePayment($transactionId, [
            //     'payment_id' => $paymentId,
            //     'payment_url' => $paymentUrl
            // ]);

            Payment::where('transaction_id', $transactionId)->update([
                'payment_id' => $paymentId,
            ]);

            Log::info('K-Pay Payment Initiated', [
                'transaction_id' => $transactionId,
                'payment_id' => $paymentId,
                'amount' => $paymentData['amount'],
                'payment_url' => $paymentUrl
            ]);

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'payment_id' => $paymentId,
                'payment_url' => $paymentUrl,
                'message' => 'Payment initiated successfully.'
            ];

        } catch (Exception $e) {
            Log::error('K-Pay Payment Initiation Failed', [
                'error' => $e->getMessage(),
                'data' => $paymentData
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate PaymentID for K-Pay
     */
    private function generatePaymentId(array $paymentData, string $transactionId): string
    {
        // Generate PaymentID in K-Pay format: timestamp + sequential number
        // Format similar to: 107524529000261676
        $timestamp = time();
        $randomSuffix = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);

        return $timestamp . $randomSuffix;
    }

    /**
     * Alternative: Register payment with K-Pay gateway (if required)
     * Use this method if K-Pay requires pre-registration
     */
    private function registerPaymentWithGateway(array $paymentData, string $transactionId): ?string
    {
        try {
            $params = $this->preparePaymentParams($paymentData, $transactionId);

            // Use HTTPS and increase timeout
            $registrationUrl = str_replace('http://', 'https://', $this->config['registration_url'] ?? 'https://www.kpay.com.kw/kpg/register.htm');

            $response = Http::timeout(60)
                ->withOptions([
                    'verify' => false, // Disable SSL verification if needed for testing
                    'connect_timeout' => 30,
                ])
                ->withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'User-Agent' => 'Laravel-KPay-Client/1.0'
                ])
                ->post($registrationUrl, $params);

            if (!$response->successful()) {
                Log::error('K-Pay registration failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'url' => $registrationUrl
                ]);
                throw new Exception('Gateway registration failed: ' . $response->status());
            }

            $responseData = $response->json();

            // Extract PaymentID from response
            $paymentId = $responseData['PaymentID'] ?? $responseData['paymentId'] ?? null;

            if (!$paymentId) {
                $paymentId = $this->extractPaymentIdFromResponse($response->body());
            }

            return $paymentId;

        } catch (Exception $e) {
            Log::error('K-Pay gateway registration error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId
            ]);
            throw $e;
        }
    }

    /**
     * Extract PaymentID from response if not in JSON format
     */
    private function extractPaymentIdFromResponse(string $responseBody): ?string
    {
        // Try different patterns based on K-Pay response format
        $patterns = [
            '/PaymentID["\s]*[:=]["\s]*([0-9]+)/i',
            '/paymentId["\s]*[:=]["\s]*([0-9]+)/i',
            '/<input[^>]*name["\s]*=["\s]*PaymentID[^>]*value["\s]*=["\s]*([0-9]+)/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $responseBody, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Prepare payment parameters for K-Pay
     */
    private function preparePaymentParams(array $paymentData, string $transactionId): array
    {
        $amount = number_format($paymentData['amount'], 3, '.', '');

        return [
            'id' => $this->merchantId,
            'password' => $this->merchantPassword,
            'action' => '1', // Purchase action
            'amt' => $amount,
            'currencycode' => $paymentData['currency'] ?? 'KWD',
            'langid' => $paymentData['language'] ?? 'ENG',
            'trackid' => $transactionId,
            'responseURL' => $this->returnUrl,
            'errorURL' => $this->returnUrl,
            'member' => $paymentData['customer_email'] ?? '',
            'udf1' => $paymentData['description'] ?? '',
            'udf2' => $paymentData['customer_phone'] ?? '',
            'udf3' => $paymentData['order_id'] ?? '',
            'udf4' => '',
            'udf5' => ''
        ];
    }

    /**
     * Generate K-Pay payment URL with PaymentID
     */
    protected function generatePaymentUrl(string $paymentId): string
    {
        return $this->gatewayUrl . '?PaymentID=' . $paymentId;
    }

    /**
     * Alternative method: Generate PaymentID locally (if K-Pay allows)
     */
    private function generateLocalPaymentId(): string
    {
        // Generate a unique PaymentID similar to K-Pay format
        // Format: timestamp + random number (adjust based on K-Pay requirements)
        return time() . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Handle payment response/callback
     */
    public function handlePaymentResponse(array $responseData): array
    {
        try {
            // K-Pay might return PaymentID instead of trackid
            $paymentId = $responseData['PaymentID'] ?? $responseData['paymentId'] ?? null;
            $transactionId = $responseData['trackid'] ?? null;

            // Try to find payment by either PaymentID or transaction_id
            $payment = null;
            if ($paymentId) {
                $payment = Payment::where('payment_id', $paymentId)->first();
            }
            if (!$payment && $transactionId) {
                $payment = Payment::where('transaction_id', $transactionId)->first();
            }

            if (!$payment) {
                throw new Exception('Payment not found');
            }

            // Verify response authenticity
            if (!$this->verifyResponse($responseData)) {
                $payment->update(['status' => Payment::STATUS_FAILED]);
                throw new Exception('Invalid response signature');
            }

            // Process response based on result
            $status = $this->determineTransactionStatus($responseData);

            // Update payment record
            $payment->update([
                'status' => $status,
                'payment_id' => $paymentId,
                'reference_id' => $responseData['ref'] ?? $responseData['reference'] ?? null,
                'response_code' => $responseData['result'] ?? $responseData['status'] ?? null,
                'response_message' => $this->getResponseMessage($responseData['result'] ?? $responseData['status'] ?? null),
                'response_data' => json_encode($responseData)
            ]);

            Log::info('K-Pay Payment Response Processed', [
                'transaction_id' => $transactionId,
                'payment_id' => $paymentId,
                'status' => $status
            ]);

            return [
                'success' => $status === Payment::STATUS_SUCCESS,
                'transaction_id' => $transactionId,
                'payment_id' => $paymentId,
                'status' => $status,
                'message' => $this->getResponseMessage($responseData['result'] ?? $responseData['status'] ?? null),
                'transaction' => $payment->fresh()
            ];

        } catch (Exception $e) {
            Log::error('K-Pay Payment Response Processing Failed', [
                'error' => $e->getMessage(),
                'response_data' => $responseData
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process payment response: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate payment data
     */
    private function validatePaymentData(array $data): void
    {
        $required = ['amount', 'customer_email'];

        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("Required field missing: {$field}");
            }
        }

        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            throw new Exception('Invalid amount');
        }

        if (!filter_var($data['customer_email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address');
        }
    }

    /**
     * Verify response authenticity (implement based on K-Pay documentation)
     */
    private function verifyResponse(array $responseData): bool
    {
        // Implement K-Pay specific verification
        // This might involve checking a hash or signature

        $requiredFields = ['PaymentID'];
        foreach ($requiredFields as $field) {
            if (!isset($responseData[$field])) {
                return false;
            }
        }

        // Add signature verification here if K-Pay provides it
        return true;
    }

    /**
     * Determine transaction status from K-Pay response
     */
    private function determineTransactionStatus(array $responseData): string
    {
        $result = $responseData['result'] ?? $responseData['status'] ?? '';

        switch (strtoupper($result)) {
            case 'CAPTURED':
            case 'SUCCESS':
            case 'APPROVED':
                return Payment::STATUS_SUCCESS;
            case 'CANCELLED':
            case 'CANCELED':
                return Payment::STATUS_CANCELLED;
            case 'PENDING':
                return Payment::STATUS_PENDING;
            default:
                return Payment::STATUS_FAILED;
        }
    }

    /**
     * Get response message for K-Pay results
     */
    private function getResponseMessage(?string $result): string
    {
        $messages = [
            'CAPTURED' => 'Payment completed successfully',
            'SUCCESS' => 'Payment completed successfully',
            'APPROVED' => 'Payment approved successfully',
            'CANCELLED' => 'Payment was cancelled by user',
            'CANCELED' => 'Payment was cancelled by user',
            'FAILED' => 'Payment failed',
            'DECLINED' => 'Payment was declined',
            'PENDING' => 'Payment is pending verification',
            'NOT CAPTURED' => 'Payment authorization failed',
            'DENIED' => 'Payment was denied',
        ];

        return $messages[strtoupper($result)] ?? 'Unknown payment status';
    }
}
