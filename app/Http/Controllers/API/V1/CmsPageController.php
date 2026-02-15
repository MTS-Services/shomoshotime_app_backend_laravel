<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\CmsPageRequest;
use App\Http\Resources\API\V1\CmsPageResource;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CmsPageController extends Controller
{
    public function index(Request $request)
    {
        try {
            $authResponse = $this->ensureAdminAccess($request);
            if ($authResponse) {
                return $authResponse;
            }

            $query = CmsPage::query()->orderByDesc('sort_order')->orderBy('id');

            if ($request->filled('type')) {
                $query->where('type', CmsPage::normalizeType($request->input('type')));
            }

            $perPage = (int) $request->input('per_page', 10);
            $pages = $query->paginate($perPage);

            return sendResponse(true, 'CMS pages fetched successfully.', CmsPageResource::collection($pages), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('CMS Page Index Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(CmsPageRequest $request)
    {
        try {
            $authResponse = $this->ensureAdminAccess($request);
            if ($authResponse) {
                return $authResponse;
            }

            $data = $request->validated();
            $data['type'] = CmsPage::normalizeType($data['type']);
            $data['created_by'] = $request->user()->id;
            $data['updated_by'] = $request->user()->id;

            $cmsPage = CmsPage::create($data);

            return sendResponse(true, 'CMS page created successfully.', new CmsPageResource($cmsPage), Response::HTTP_CREATED);
        } catch (Throwable $e) {
            Log::error('CMS Page Store Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(CmsPageRequest $request)
    {
        $authResponse = $this->ensureAdminAccess($request);
        if ($authResponse) {
            return $authResponse;
        }

        $cmsPage = CmsPage::findOrFail($request->input('id'));

        return sendResponse(true, 'CMS page fetched successfully.', new CmsPageResource($cmsPage), Response::HTTP_OK);
    }

    public function update(CmsPageRequest $request)
    {
        try {
            $authResponse = $this->ensureAdminAccess($request);
            if ($authResponse) {
                return $authResponse;
            }

            $data = $request->validated();
            $cmsPage = CmsPage::findOrFail($data['id']);

            if (isset($data['type'])) {
                $data['type'] = CmsPage::normalizeType($data['type']);
            }
            $data['updated_by'] = $request->user()->id;

            $cmsPage->update(collect($data)->except('id')->toArray());

            return sendResponse(true, 'CMS page updated successfully.', new CmsPageResource($cmsPage->refresh()), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('CMS Page Update Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(CmsPageRequest $request)
    {
        try {
            $authResponse = $this->ensureAdminAccess($request);
            if ($authResponse) {
                return $authResponse;
            }

            $cmsPage = CmsPage::findOrFail($request->input('id'));
            $cmsPage->delete();

            return sendResponse(true, 'CMS page deleted successfully.', null, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('CMS Page Delete Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function publicShow(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                return sendResponse(false, 'Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $normalizedType = CmsPage::normalizeType($request->input('type'));
            $cmsPage = CmsPage::where('type', $normalizedType)
                ->where('is_active', true)
                ->first();

            if (! $cmsPage) {
                return sendResponse(false, 'CMS page not found.', null, Response::HTTP_NOT_FOUND);
            }

            return sendResponse(true, 'CMS page fetched successfully.', new CmsPageResource($cmsPage), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('CMS Page Public Show Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function ensureAdminAccess(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
        }

        if (! $user->isAdmin()) {
            return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
        }

        return null;
    }
}
