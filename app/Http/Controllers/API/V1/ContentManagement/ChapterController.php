<?php

namespace App\Http\Controllers\API\V1\ContentManagement;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\ChapterResource;
use App\Services\ContentManagement\ChapterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ChapterController extends Controller
{

     protected ChapterService $service;

    public function __construct(ChapterService $service)
    {
        $this->service = $service;
    }
    public function getChapters(Request $request)
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
            
          $chapters = $this->service->getChaptersByContent($contentId)->paginate($request->integer('per_page', 10));

            return sendResponse(
                true,
                'Chapters fetched successfully.',
                ChapterResource::collection($chapters),
                Response::HTTP_OK
            );

        } catch (Throwable $e) {
            Log::error('Get Chapters Error: '.$e->getMessage());

            return sendResponse(
                false,
                'Something went wrong.',
                null,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
