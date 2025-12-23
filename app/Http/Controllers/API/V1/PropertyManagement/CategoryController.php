<?php

namespace App\Http\Controllers\API\V1\PropertyManagement;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\PropertyManagement\CategoryResource;
use App\Models\Category;
use App\Services\Category\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function categories(): JsonResponse
    {
        try {
            $categories = $this->categoryService->getCategories()->active()->get();
            return sendResponse(true, 'تم استرجاع الاقسام بنجاح.', CategoryResource::collection($categories), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to get categories: ', ['exception' => $error]);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
