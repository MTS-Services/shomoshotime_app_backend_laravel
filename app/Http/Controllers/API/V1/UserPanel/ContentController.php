<?php

namespace App\Http\Controllers\API\V1\UserPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\ContentResource;
use App\Services\ContentManagement\ContentService;
use App\Services\ContentManagement\FlashCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            if ($request->has('search')) {
                $searchQuery = $request->input('search');
                $query->whereLike('title', $searchQuery)
                    ->orWhereLike('subtitle', $searchQuery); // note OR instead of chaining WHERE

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
            $flashCards = $query->paginate($request->input('per_page', 10));

            return sendResponse(true, ' Flash cards data fetched successfully.', ContentResource::collection($flashCards), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
