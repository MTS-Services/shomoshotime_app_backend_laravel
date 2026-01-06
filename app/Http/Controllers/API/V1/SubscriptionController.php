<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\SubscriptionRequest;
use App\Http\Resources\API\V1\SubscriptionResource;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SubscriptionController extends Controller
{
    protected SubscriptionService $service;

    public function __construct(SubscriptionService $service)
    {
        $this->service = $service;
    }

    public function getSubscriptions(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
            }
            $query = $this->service->getAllSubscriptions();
            $subscriptions = $query->paginate($request->input('per_page', 10));

            return sendResponse(true, 'Subscriptions data fetched successfully.', SubscriptionResource::collection($subscriptions), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(SubscriptionRequest $request)
    {
        try {
            $user = $request->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
            }

            $data = $request->all();
            $subscription = $this->service->createSubscription($data);

            return sendResponse(true, 'Subscription created successfully.', new SubscriptionResource($subscription), Response::HTTP_CREATED);
        } catch (Throwable $e) {
            Log::error('Create Subscription Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

      public function update(SubscriptionRequest $request, $id)
    {
        try {
            $user = $request->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
            }

            $subscription = $this->service->findData($id);
            $data = $request->all();

            // Update subscription via service
            $updatedsubscription = $this->service->updateSubscription($subscription, $data);

            return sendResponse(true, 'Subscription updated successfully.', new SubscriptionResource($updatedsubscription), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Update Subscription Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

        public function delete($id)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
            }

            $subscription = $this->service->findData($id);
            $this->service->deleteSubscription($subscription);

            return sendResponse(true, 'Subscription deleted successfully.', null, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Delete Subscription Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
