<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\UserSubscriptionRequest;
use App\Http\Resources\API\V1\UserSubscriptionResource;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserSubscriptionController extends Controller
{
       protected SubscriptionService $service;

    public function __construct(SubscriptionService $service)
    {
        $this->service = $service;
    }

    

    public function store(UserSubscriptionRequest $request)
    {
        try {
            $user = $request->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            $data = $request->all();
            $subscription = $this->service->createUserSubscription($data);
            $subscription->load(['user', 'subscription']);

            return sendResponse(true, 'User Subscription created successfully.', new UserSubscriptionResource($subscription), Response::HTTP_CREATED);
        } catch (Throwable $e) {
            Log::error('Create User Subscription Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
