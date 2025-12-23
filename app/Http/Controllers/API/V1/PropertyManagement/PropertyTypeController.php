<?php

namespace App\Http\Controllers\API\V1\PropertyManagement;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\PropertyManagement\PropertyTypeResource;
use App\Services\PropertyManagement\PropertyTypeService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PropertyTypeController extends Controller
{
    protected PropertyTypeService $propertyTypesService;

    public function __construct(PropertyTypeService $propertyTypesService)
    {
        $this->propertyTypesService = $propertyTypesService;
    }

    public function propertyTypes(): JsonResponse
    {
        try {
            $propertyTypes = $this->propertyTypesService->getPropertyTypes('id', 'asc')->get();
            if ($propertyTypes->isEmpty()) {
                return sendResponse(false, 'لم يتم العثور على أنواع العقارات', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'تم استرجاع قائمة أنواع العقارات بنجاح.', PropertyTypeResource::collection($propertyTypes), Response::HTTP_OK);
        } catch (Throwable $error) {
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
