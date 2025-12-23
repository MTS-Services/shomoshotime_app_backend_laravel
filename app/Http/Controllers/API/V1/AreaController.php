<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\AreaResource;
use App\Services\Area\AreaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AreaController extends Controller
{
    protected AreaService $areaService;

    public function __construct(AreaService $areaService)
    {
        $this->areaService = $areaService;
    }

    public function areas(Request $request): JsonResponse
    {
        try {
            $areas = $this->areaService->getAreas('id', 'asc')->active()->get();
            if ($areas->isEmpty()) {
                return sendResponse(false, 'لم يتم العثور على مناطق.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'تم استرجاع قائمة المناطق بنجاح.', AreaResource::collection($areas), Response::HTTP_OK);
        } catch (Throwable $error) {
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
