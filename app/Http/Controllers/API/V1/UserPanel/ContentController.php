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
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContentController extends Controller
{
    protected ContentService $service;

    protected FlashCardService $flashCardService;

    public function __construct(ContentService $service, FlashCardService $flashCardService)
    {
        $this->service = $service;
        $this->flashCardService = $flashCardService;
    }


    public function stream(Request $request, string $filename)
    {
        $path = $filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Audio not found');
        }

        $fullPath = Storage::disk('public')->path($path);
        $fileSize = filesize($fullPath);
        $mimeType = 'audio/mpeg';

        $range = $request->header('Range');

        // No range request - use BinaryFileResponse
        if (!$range) {
            $response = new BinaryFileResponse($fullPath);
            $response->headers->set('Content-Type', $mimeType);
            $response->headers->set('Accept-Ranges', 'bytes');
            $response->headers->set('Cache-Control', 'public, max-age=31536000');
            return $response;
        }

        // Parse range header
        if (!preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
            abort(416, 'Invalid range');
        }

        $start = intval($matches[1]);
        $end = !empty($matches[2]) ? intval($matches[2]) : $fileSize - 1;

        // Ensure valid range
        if ($start >= $fileSize || $start < 0 || $end >= $fileSize) {
            abort(416, 'Range not satisfiable');
        }

        $end = min($end, $fileSize - 1);
        $length = $end - $start + 1;

        // Use StreamedResponse for range requests
        $stream = new StreamedResponse(function () use ($fullPath, $start, $length) {
            $file = fopen($fullPath, 'rb');
            if ($file === false) {
                return;
            }

            fseek($file, $start);

            $remaining = $length;
            while ($remaining > 0 && !feof($file)) {
                $chunkSize = min(8192, $remaining);
                $data = fread($file, $chunkSize);
                if ($data === false) {
                    break;
                }
                echo $data;
                $remaining -= strlen($data);
                flush();
            }

            fclose($file);
        }, 206);

        $stream->headers->set('Content-Type', $mimeType);
        $stream->headers->set('Content-Length', $length);
        $stream->headers->set('Content-Range', "bytes {$start}-{$end}/{$fileSize}");
        $stream->headers->set('Accept-Ranges', 'bytes');
        $stream->headers->set('Cache-Control', 'public, max-age=31536000');

        return $stream;
    }

    public function nextPage(Request $request)
    {
        try {
            $user = request()->user();
            if (!$user) {
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
            if (!$content) {
                return sendResponse(false, 'Content not found', null, Response::HTTP_NOT_FOUND);
            }

            $nextPageNumber = $this->service->storeNextPageData($user->id, $contentId, $pageNumber);

            return sendResponse(true, 'Next page data stored successfully.', $nextPageNumber, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Next Page Error: ' . $e->getMessage());

            return sendResponse(false, 'Something went wrong.' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function studyGuides(Request $request)
    {
        try {
            $user = request()->user();
            if (!$user) {
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
            Log::error('Get Todos Error: ' . $e->getMessage());

            return sendResponse(false, 'Something went wrong.' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function flashCards(Request $request)
    {
        try {
            $user = request()->user();
            if (!$user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            $category = $request->input('category');
            $query = $this->flashCardService->getFlashCards($category);
            $query->withCount(['flashCardActivities', 'flashCards']);
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
            Log::error('Get Todos Error: ' . $e->getMessage());

            return sendResponse(false, 'Something went wrong.' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function nextQuestion(Request $request)
    {
        try {
            $user = request()->user();
            if (!$user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }
            $validator = Validator::make($request->all(), [
                'content_id' => 'required|integer|exists:contents,id',
                'card_id' => 'required|integer|exists:flash_cards,id',
            ]);
            if ($validator->fails()) {
                return sendResponse(false, 'Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $data = $validator->validated();
            $contentId = $data['content_id'];
            $cardId = $data['card_id'];
            $flashCard = $this->flashCardService->findFlashCard($contentId, $cardId);
            if (!$flashCard) {
                return sendResponse(false, 'This flash card does not belong to the given content.', null, Response::HTTP_NOT_FOUND);
            }
            $this->flashCardService->storeNextQuestionData($user->id, $contentId, $cardId);

            return sendResponse(true, 'Next question data stored successfully.', null, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Next Question Error: ' . $e->getMessage());

            return sendResponse(false, 'Something went wrong.' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function flashCardSets(Request $request)
    {
        try {
            $user = request()->user();
            if (!$user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }
            $contentId = $request->input('content_id');
            if (!$contentId) {
                return sendResponse(false, 'content_id is required', null, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $flashCardSets = $this->flashCardService->getFlashCardsByContent($contentId);
            $contents = $flashCardSets->paginate($request->input('per_page', 10));

            return sendResponse(true, ' Flash card sets data fetched successfully.', FlashCardResource::collection($contents), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: ' . $e->getMessage());

            return sendResponse(false, 'Something went wrong.' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}