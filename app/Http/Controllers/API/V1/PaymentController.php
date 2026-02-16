<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\PaymentResource;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscriptions;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PaymentController extends Controller
{
    protected PaymentService $service;

    public function __construct(PaymentService $service)
    {
        $this->service = $service;
    }

    private function buildPlanPayload(User $user): array
    {
        $activeSubscription = UserSubscriptions::with('subscription')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->latest('ends_at')
            ->first()?->subscription;

        $recommendedSubscription = Subscription::active()
            ->orderBy('price')
            ->first();

        return [
            'current' => $this->formatPlanData($activeSubscription),
            'recommended' => $this->formatPlanData($recommendedSubscription),
        ];
    }

    private function formatPlanData(?Subscription $subscription): ?array
    {
        if (! $subscription) {
            return null;
        }

        return [
            'id' => $subscription->id,
            'name' => $subscription->duration,
            'tag' => $subscription->tag,
            'price' => $subscription->price,
            'features' => $subscription->features,
            'status' => $subscription->status_label ?? null,
        ];
    }

    public function store(Request $request)
    {
        try {
            $user = $request->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            $request->validate([
                'subscription_id' => 'required|exists:subscriptions,id',
                'amount' => 'required|numeric|min:0',
                'payment_intent_data' => 'nullable',
            ]);
            $result = $this->service->createPaymentWithSubscription($request->all(), $user->id); // service call

            $planPayload = $this->buildPlanPayload($user);

            return sendResponse(true, 'Payment and subscription created successfully.', [
                'payment' => new PaymentResource($result['payment']),
                'plans' => $planPayload,
            ], Response::HTTP_CREATED);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return sendResponse(false, 'Validation failed', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Throwable $e) {
            return sendResponse(false, 'Something went wrong while processing payment.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
