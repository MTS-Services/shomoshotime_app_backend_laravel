<?php

namespace App\Http\Controllers\API\V1\UserPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\QuestionSetResource;
use App\Services\QuestionManagement\QuestionSetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class QuestionController extends Controller
{
     protected QuestionSetService $questionSetService;


    public function __construct(QuestionSetService $questionSetService)
    {
        $this->questionSetService = $questionSetService;
    }


    public function getQuestionSets(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }
            $questions = $this->questionSetService->getQuestionSets();
            $contents = $questions->paginate($request->input('per_page', 10));
            
            return sendResponse(true, ' Questions Set data fetched successfully.', QuestionSetResource::collection($contents), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
