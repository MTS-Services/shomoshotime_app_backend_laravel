<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\AgentResource;
use App\Models\CompanyInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AgentController extends Controller
{
    public function agents()
    {
        try {
            $agents = CompanyInformation::with(['user.properties.views'])->get();

            if (!$agents || $agents->isEmpty()) {
                Log::error('Failed to get agents.');
                return sendResponse(false, 'No agents found.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(
                true,
                'Agents retrieved successfully.',
                AgentResource::collection($agents),
                Response::HTTP_OK
            );
        } catch (Throwable $error) {
            Log::error('Failed to get agents: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to get agents.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function agent(string $id)
    {
        try {
            $agent = CompanyInformation::with(['user.properties.views'])->find($id);
            if (!$agent) {
                Log::error('Failed to get agent.');
                return sendResponse(false, 'Agent not found.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(
                true,
                'Agent retrieved successfully.',
                new AgentResource($agent),
                Response::HTTP_OK
            );
        } catch (Throwable $error) {
            Log::error('Failed to get agent: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to get agent.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
