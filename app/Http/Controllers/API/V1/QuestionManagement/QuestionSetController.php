<?php

namespace App\Http\Controllers\API\V1\QuestionManagement;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\QuestionSetCollection;
use Symfony\Component\HttpFoundation\Response;
use App\Services\QuestionManagement\QuestionSetService;

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
            $questionSets->setPageName('page');

            return sendResponse(true, ' Question sets fetched successfully.', new QuestionSetCollection($questionSets),Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
