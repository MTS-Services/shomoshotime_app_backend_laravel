<?php

namespace App\Http\Controllers\API\V1\UserPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SubscriptionService;
use App\Http\Resources\API\V1\SubscriptionResource;
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
            $query = $this->service->getAllSubs();
            $subscriptions = $query->paginate($request->input('per_page', 10));

            return sendResponse(true, 'Subscriptions data fetched successfully.', SubscriptionResource::collection($subscriptions), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
