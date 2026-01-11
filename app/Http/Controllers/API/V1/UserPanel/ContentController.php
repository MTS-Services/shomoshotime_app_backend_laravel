<?php

namespace App\Http\Controllers\API\V1\UserPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\ContentResource;
use App\Http\Resources\API\V1\FlashCardResource;
use App\Services\ContentManagement\ContentService;
use App\Services\ContentManagement\FlashCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ContentController extends Controller
{
    protected ContentService $service;

    protected FlashCardService $flashCardService;

    public function __construct(ContentService $service, FlashCardService $flashCardService)
    {
        $this->service = $service;
        $this->flashCardService = $flashCardService;
    }

    public function nextPage(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }
            $validator = Validator::make($request->all(), [
                'content_id' => 'required|integer|exists:contents,id',
                'page_number' => 'required|integer|min:1',
            ]);
            if ($validator->fails()) {
                return sendResponse(false, 'Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $data = $validator->validated();
            $contentId = $data['content_id'];
            $pageNumber = $data['page_number'];
            $content = $this->service->findContent($contentId);
            if (! $content) {
                return sendResponse(false, 'Content not found', null, Response::HTTP_NOT_FOUND);
            }

            $nextPageNumber = $this->service->storeNextPageData($user->id, $contentId, $pageNumber);

            return sendResponse(true, 'Next page data stored successfully.', $nextPageNumber, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Next Page Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function studyGuides(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }
            $file_type = $request->input('file_type');
            $category = $request->input('category');
            $query = $this->service->getContents($category, $file_type);
            $query->withCount('studyGuideActivities');
            if ($request->has('search')) {
                $searchQuery = $request->input('search');
                $query->whereLike('title', $searchQuery)
                    ->orWhereLike('subtitle', $searchQuery); 
                $contents = $query->paginate($request->input('per_page', 10));

                return sendResponse(true, 'Search data fetched successfully.', ContentResource::collection($contents), Response::HTTP_OK);
            }
            $contents = $query->paginate($request->input('per_page', 10));

            return sendResponse(true, 'Study guides data fetched successfully.', ContentResource::collection($contents), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function flashCards(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            $category = $request->input('category');
            $query = $this->flashCardService->getFlashCards($category);
            if ($request->has('search')) {
                $searchQuery = $request->input('search');
                $query->whereLike('title', $searchQuery)
                    ->orWhereLike('subtitle', $searchQuery); // note OR instead of chaining WHERE

                $contents = $query->paginate($request->input('per_page', 10));

                return sendResponse(true, 'Search data fetched successfully.', ContentResource::collection($contents), Response::HTTP_OK);
            }
            $flashCards = $query->paginate($request->input('per_page', 10));

            return sendResponse(true, ' Flash cards data fetched successfully.', ContentResource::collection($flashCards), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function flashCardSets(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }
            $contentId = $request->input('content_id');
            if (! $contentId) {
                return sendResponse(false, 'content_id is required', null, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $flashCardSets = $this->flashCardService->getFlashCardsByContent($contentId);
            $contents = $flashCardSets->paginate($request->input('per_page', 10));

            return sendResponse(true, ' Flash card sets data fetched successfully.', FlashCardResource::collection($contents), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
