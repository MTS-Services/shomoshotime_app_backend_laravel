<?php

namespace App\Http\Controllers\API\V1\QuestionManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\QuestionSetRequest;
use App\Http\Resources\API\V1\QuestionSetResource;
use App\Services\QuestionManagement\QuestionSetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class QuestionSetController extends Controller
{
    protected QuestionSetService $service;

    public function __construct(QuestionSetService $service)
    {
        $this->service = $service;
    }

    public function getQuestionSets(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
            }

            $query = $this->service->getQuestionSets();
            if ($request->has('search')) {
                $searchQuery = $request->input('search');
                $query->whereLike('category', $searchQuery)
                    ->whereLike('title', $searchQuery);
            }
            $questionSets = $query->paginate($request->input('per_page', 10));

            return sendResponse(true, ' Question sets fetched successfully.', QuestionSetResource::collection($questionSets), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(QuestionSetRequest $request)
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
            $question_set = $this->service->createQuestion($data);

            return sendResponse(true, 'Question Set created successfully.', new QuestionSetResource($question_set), Response::HTTP_CREATED);
        } catch (Throwable $e) {
            Log::error('Create Question Set Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(QuestionSetRequest $request, $id)
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
            $question_set = $this->service->updateQuestion($findData, $data);

            return sendResponse(true, 'Question Set updated successfully.', new QuestionSetResource($question_set), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Update Question Set Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
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

            return sendResponse(true, 'Question Set deleted successfully.', null, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Delete Question Set Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
