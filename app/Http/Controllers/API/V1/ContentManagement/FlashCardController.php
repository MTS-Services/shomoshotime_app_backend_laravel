<?php

namespace App\Http\Controllers\API\V1\ContentManagement;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\FlashCardCollection;
use App\Services\ContentManagement\FlashCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class FlashCardController extends Controller
{
    protected FlashCardService $service;

    public function __construct(FlashCardService $service)
    {
        $this->service = $service;
    }

    public function getFlashCards(Request $request)
    {
        try {
            $user = $request->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_FORBIDDEN);
            }

            // content_id must be provided
            $contentId = $request->input('content_id');

            if (! $contentId) {
                return sendResponse(false, 'content_id is required', null, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $flashCards = $this->service->getFlashCardsByContent($contentId)->get();

            return sendResponse(
                true,
                'Flash cards fetched successfully.',
                new FlashCardCollection($flashCards),
                Response::HTTP_OK
            );

        } catch (Throwable $e) {
            Log::error('Get FlashCards Error: '.$e->getMessage());

            return sendResponse(
                false,
                'Something went wrong.',
                null,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function create(Request $request)
    {
        try {
            $user = $request->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_FORBIDDEN);
            }
            $data = $request->all();
            $result = $this->service->createFlashCard($data);

            if (is_array($result)) {
                return sendResponse(false, $result['message'], null, $result['status']);
            }

            return sendResponse(true, 'Flash card created successfully.', $result, Response::HTTP_CREATED);

        } catch (Throwable $e) {
            Log::error('Create FlashCard Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
