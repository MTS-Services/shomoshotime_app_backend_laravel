<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\PaymentResource;
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
                'payment_intent_data' => 'nullable|array',
            ]);

            $result = $this->service->createPaymentWithSubscription($request->all(), $user->id); // service call

            return sendResponse(true, 'Payment and subscription created successfully.', ['payment' => new PaymentResource($result['payment']),], Response::HTTP_CREATED);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return sendResponse(false, 'Validation failed', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Throwable $e) {
            return sendResponse(false, 'Something went wrong while processing payment.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
