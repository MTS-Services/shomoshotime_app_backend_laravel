<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscriptions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function createPayment(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = Auth::id();

            return Payment::create($data);
        });
    }

    public function createPaymentWithSubscription(array $data, int $userId)
    {
        return DB::transaction(function () use ($data, $userId) {

            // Create payment
            $paymentIntentPayload = $data['payment_intent_data'] ?? null;
            if (is_string($paymentIntentPayload)) {
                $decodedPayload = json_decode($paymentIntentPayload, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $paymentIntentPayload = $decodedPayload;
                }
            }

            $paymentData = [
                'user_id' => $userId,
                'subscription_id' => $data['subscription_id'],
                'amount' => $data['amount'] ?? 0,
                'currency' => $data['currency'] ?? 'USD',
                'payment_method' => $data['payment_method'] ?? null,
                'transaction_id' => $data['transaction_id'] ?? null,
                'status' => $data['status'] ?? 1,
                'payment_intent_data' => $paymentIntentPayload,
                'created_by' => $userId,
            ];

            $payment = Payment::create($paymentData);

            // Get subscription
            $subscription = Subscription::findOrFail($data['subscription_id']);

            // Deactivate previous active subscriptions
            UserSubscriptions::where('user_id', $userId)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Calculate dates
            $startsAt = Carbon::now();
            $endsAt = $this->calculateEndDate($startsAt, $subscription->duration);

            // Create user subscription
            $userSubscription = UserSubscriptions::create([
                'user_id' => $userId,
                'payment_id' => $payment->id,
                'subscription_id' => $data['subscription_id'],
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'is_active' => true,
                'created_by' => $userId,
            ]);
            User::whereKey($userId)->update(['is_premium' => true]);
            // Load relationships
            $payment->load(['user', 'subscription', 'userSubscriptions']);
            $userSubscription->load(['user', 'subscription', 'payment']);

            return [
                'payment' => $payment,
                'user_subscription' => $userSubscription,
            ];
        });
    }

    /**
     * Calculate end date based on subscription duration
     */
    private function calculateEndDate(Carbon $startDate, string $duration): Carbon
    {
        $endDate = $startDate->copy();

        switch (strtolower($duration)) {
            case 'weekly':
                $endDate->addWeek();
                break;
            case 'monthly':
                $endDate->addMonth();
                break;
            case 'yearly':
                $endDate->addYear();
                break;
            default:
                $endDate->addMonth();
                break;
        }

        return $endDate;
    }
}
