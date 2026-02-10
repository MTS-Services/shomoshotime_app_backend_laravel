<?php

namespace App\Http\Controllers\API\V1\ContentManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\ContentRequest;
use App\Http\Resources\API\V1\ContentResource;
use App\Services\ContentManagement\ContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ContentController extends Controller
{
    protected ContentService $service;

    public function __construct(ContentService $service)
    {
        $this->service = $service;
    }

    public function getContents(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
            }
            $type = $request->input('type');
            $query = $this->service->getContents($type);
            $contents = $query->paginate($request->input('per_page', 10));

            return sendResponse(true, 'Contents data fetched successfully.', ContentResource::collection($contents), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(ContentRequest $request)
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
            $file = $request->file('file') ?? null;
            $content = $this->service->createContent($data, $file);

            return sendResponse(true, 'Content created successfully.', new ContentResource($content), Response::HTTP_CREATED);
        } catch (Throwable $e) {
            Log::error('Create Content Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(ContentRequest $request, $id)
    {
        try {
            $user = $request->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
            }

            $content = $this->service->findContent($id);
            $data = $request->all();
            $file = $request->file('file') ?? null;

            // Update content via service
            $updatedContent = $this->service->updateContent($content, $data, $file);

            return sendResponse(true, 'Content updated successfully.', new ContentResource($updatedContent), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Update Content Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
            }

            $content = $this->service->findContent($id);
            $this->service->deleteContent($content);

            return sendResponse(true, 'Content deleted successfully.', null, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Delete Content Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
