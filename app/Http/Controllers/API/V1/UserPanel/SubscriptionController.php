<?php

namespace App\Http\Controllers\API\V1\UserPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\SubscriptionResource;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SubscriptionController extends Controller
{
    protected SubscriptionService $service;

    protected static array $staticSecrets = [];

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
            $query = $this->service->getAllSubs();
            $subscriptions = $query->paginate($request->input('per_page', 10));

            return sendResponse(true, 'Subscriptions data fetched successfully.', SubscriptionResource::collection($subscriptions), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function stripeKey(Request $request)
    {
        try {
            $user = $request->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }
            $publishableKey = config('services.stripe.key');
            $secretKey = config('services.stripe.secret');

            return sendResponse(true, 'Key fetched successfully.', [
                'publishable_key' => $publishableKey,
                'secret_key' => $secretKey,
            ], Response::HTTP_OK);

        } catch (Throwable $e) {
            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
