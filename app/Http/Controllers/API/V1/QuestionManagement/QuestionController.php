<?php

namespace App\Http\Controllers\API\V1\QuestionManagement;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\QuestionResource;
use App\Services\QuestionManagement\QuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class QuestionController extends Controller
{
        protected QuestionService $service;

    public function __construct(QuestionService $service)
    {
        $this->service = $service;
    }

    public function getQuestions(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
            }
              $questionId = $request->input('question_set_id');

            if (! $questionId) {
                return sendResponse(false, 'question_set_id is required', null, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $query = $this->service->getQuestions($questionId);
            $questions = $query->paginate($request->input('per_page', 10));

            return sendResponse(true, ' Question fetched successfully.', QuestionResource::collection($questions), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
