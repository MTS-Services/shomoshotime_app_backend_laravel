<?php

namespace App\Http\Controllers\API\V1\QuestionManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\QuestionRequest;
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

    public function store(QuestionRequest $request)
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
            $question = $this->service->createQuestion($data);
            $question->load('questionSet');

            return sendResponse(true, 'Question created successfully.', new QuestionResource($question), Response::HTTP_CREATED);
        } catch (Throwable $e) {
            Log::error('Create Question Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(QuestionRequest $request, $id)
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
            $findData = $this->service->findData($id);
            $question = $this->service->updateQuestion($findData, $data);
            $question->load('questionSet');

            return sendResponse(true, 'Question updated successfully.', new QuestionResource($question), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Update Question Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
            }
            $findData = $this->service->findData($id);
            $this->service->deleteQuestion($findData);

            return sendResponse(true, 'Question deleted successfully.', null, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Delete Question Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
