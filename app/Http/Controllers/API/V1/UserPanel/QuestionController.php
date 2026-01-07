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


    public function submitAnswer(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }
            $qus_set_id = $request->input('question_set_id');
            if (! $qus_set_id) {
                return sendResponse(false, 'content_id is required', null, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $qus_id = $request->input('question_id');
            if (! $qus_set_id) {
                return sendResponse(false, 'content_id is required', null, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $answer = $request->input('answer');
            if (! $answer) {
                return sendResponse(false, 'answer is required', null, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $result = $this->questionSetService->submitAnswer($qus_set_id,$qus_id, $answer);
            return sendResponse(true, 'Answer submitted successfully.', $result, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
