<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\CmsPageResource;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CmsPageController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($response = $this->ensureAdminAccess($request)) {
                return $response;
            }

            if (! $request->filled('type')) {
                return sendResponse(false, 'Type is required.', null, Response::HTTP_BAD_REQUEST);
            }

            $type = CmsPage::normalizeType($request->input('type'));

            $page = CmsPage::query()
                ->where('type', $type)
                ->orderByDesc('sort_order')
                ->first();

            if (! $page) {
                return sendResponse(false, 'Data not found.', null, Response::HTTP_NOT_FOUND);
            }

            $typeLabel = CmsPage::labelForType($type);

            return sendResponse(true, $typeLabel.' page fetched successfully.', new CmsPageResource($page), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Data Index Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return sendResponse(false, 'Something went wrong. '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeAndUpdate(Request $request)
    {
        try {
            $authResponse = $this->ensureAdminAccess($request);
            if ($authResponse) {
                return $authResponse;
            }

            $rules = [
                'type' => ['required', 'string', 'max:255', Rule::in(CmsPage::allowedTypes())],
                'content' => ['required', 'string'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return sendResponse(false, 'Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data = $validator->validated();
            $data['type'] = CmsPage::normalizeType($data['type']);

            [$cmsPage, $created] = CmsPage::saveByType($data, $request->user()->id);

            $typeLabel = CmsPage::labelForType($cmsPage->type);
            $message = $created
                ? $typeLabel.' page created successfully.'
                : $typeLabel.' page updated successfully.';
            $status = $created ? Response::HTTP_CREATED : Response::HTTP_OK;

            return sendResponse(true, $message, new CmsPageResource($cmsPage), $status);
        } catch (Throwable $e) {
            Log::error('Data Store/Update Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

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

            $cmsPage = CmsPage::where('type', CmsPage::normalizeType($request->input('type')))
                ->first();

            if (! $cmsPage) {
                return sendResponse(false, 'Data not found.', null, Response::HTTP_NOT_FOUND);
            }

            return sendResponse(true, CmsPage::labelForType($cmsPage->type).' page fetched successfully.', new CmsPageResource($cmsPage), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Data Public Show Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

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
