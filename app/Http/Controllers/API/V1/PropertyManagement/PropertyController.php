<?php

namespace App\Http\Controllers\API\V1\PropertyManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\PropertyManagement\PropertyCreateRequest;
use App\Http\Requests\API\V1\PropertyManagement\PropertyUpdateRequest;
use App\Http\Resources\API\V1\PropertyManagement\PropertyResource;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyView;
use App\Services\FirebaseNotificationService;
use App\Services\PropertyManagement\PropertyService;
use GuzzleHttp\Promise\Create;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PropertyController extends Controller
{
    protected PropertyService $propertyService;
    protected FirebaseNotificationService $firebaseNotificationService;

    public function __construct(PropertyService $propertyService, FirebaseNotificationService $firebaseNotificationService)
    {
        $this->propertyService = $propertyService;
        $this->firebaseNotificationService = $firebaseNotificationService;
    }

    // ============================== Unauthenticated User Methods ==============================
    public function publicProperties(Request $request): JsonResponse
    {
        try {
            // Start with the base query from the PropertyService and use the 'open' scope
            $query = $this->propertyService->getProperties()->open();


            // Filter by transactionType (which maps to category name)
            if ($request->has('CategoryId')) {
                // Use the 'category' relationship to filter by category name
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('id', $request->input('CategoryId'));
                });
            }

            // Filter by multiple propertyTypeID

            if ($request->has('propertyTypeId')) {
                // Use the 'propertyType' relationship to filter by property type name
                $query->whereHas('propertyType', function ($q) use ($request) {
                    $q->whereIn('id', explode(',', $request->input('propertyTypeId')));
                });
            }

            // Filter by Multiple region ID
            if ($request->has('regionId')) {
                $query->whereHas('area', function ($q) use ($request) {
                    $q->whereIn('id', explode(',', $request->input('regionId')));
                });
            }

            // Filter by price range
            if ($request->has('minPrice') && $request->has('maxPrice')) {
                $minPrice = $request->input('minPrice');
                $maxPrice = $request->input('maxPrice');
                $query->whereBetween('price', [$minPrice, $maxPrice]);
            } elseif ($request->has('minPrice')) {
                $query->where('price', '>=', $request->input('minPrice'));
            } elseif ($request->has('maxPrice')) {
                $query->where('price', '<=', $request->input('maxPrice'));
            }

            // Filter by search text (on title and description)
            if ($request->has('searchText')) {
                $searchText = $request->input('searchText');
                $query->where(function ($q) use ($searchText) {
                    $q->where('title', 'like', '%' . $searchText . '%')
                        ->orWhere('description', 'like', '%' . $searchText . '%');
                });
            }

            // Execute the query with eager loading
            $properties = $query->with(['primaryImage', 'images', 'views'])->get();

            // Check if any properties were found after filtering
            if ($properties->isEmpty()) {
                return sendResponse(true, 'No properties found matching the filter criteria.', null, Response::HTTP_NOT_FOUND);
            }

            return sendResponse(true, 'All properties retrieved successfully.', PropertyResource::collection($properties), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to retrieve public properties: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to retrieve public properties.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // ============================== Single Property ==============================
    public function publicProperty($id): JsonResponse
    {
        try {
            $property = $this->propertyService->getProperty(encrypt($id), 'id')->open()->first();
            if (!$property) {
                Log::error('Property not found.', ['exception' => 'Property not found.']);
                return sendResponse(false, 'Property not found.', null, Response::HTTP_NOT_FOUND);
            }
            $property->load(['primaryImage', 'images', 'views']);
            return sendResponse(true, 'Property retrieved successfully.', new PropertyResource($property), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to retrieve public property: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to retrieve property.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function view($id): JsonResponse
    {
        try {
            $property = $this->propertyService->updateViewCount(encrypt($id));
            if (!$property) {
                Log::error('Property not found.', ['exception' => 'Property not found.']);
                return sendResponse(false, 'Property not found.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'View count updated successfully.', null, Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to update view count: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to update view count.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // ============================== Authenticated User Methods ==============================


    public function all(): JsonResponse
    {
        try {
            $properties = $this->propertyService->getProperties()->self()->with(['primaryImage', 'images', 'views'])->get();
            if ($properties->isEmpty()) {
                Log::error('No properties found for this user.', ['exception' => 'No properties found.']); // Improved log message
                return sendResponse(false, 'No properties found for this user.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'All user properties retrieved successfully.', PropertyResource::collection($properties), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to retrieve all user properties: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to retrieve all user properties.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function open(): JsonResponse
    {
        try {
            $properties = $this->propertyService->getProperties()->self()->open()->with(['primaryImage', 'images', 'views'])->get();
            if ($properties->isEmpty()) {
                Log::error('No open properties found for this user.', ['exception' => 'No properties found.']);
                return sendResponse(false, 'No open properties found for this user.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Open properties retrieved successfully.', PropertyResource::collection($properties), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to retrieve open properties: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to retrieve open properties.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function pending(): JsonResponse
    {
        try {
            $properties = $this->propertyService->getProperties()->self()->pending()->with(['primaryImage', 'images', 'views'])->get();
            if ($properties->isEmpty()) {
                Log::error('No pending properties found for this user.', ['exception' => 'No properties found.']);
                return sendResponse(false, 'No pending properties found for this user.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Pending properties retrieved successfully.', PropertyResource::collection($properties), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to retrieve pending properties: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to retrieve pending properties.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function archive(): JsonResponse
    {
        try {
            $properties = $this->propertyService->getProperties()->self()->archived()->with(['primaryImage', 'images', 'views'])->get();
            if ($properties->isEmpty()) {
                Log::error('No archived properties found for this user.', ['exception' => 'No properties found.']);
                return sendResponse(false, 'No archived properties found for this user.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Archived properties retrieved successfully.', PropertyResource::collection($properties), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to retrieve archived properties: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to retrieve archived properties.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function sold(): JsonResponse
    {
        try {
            $properties = $this->propertyService->getProperties()->self()->sold()->with(['primaryImage', 'images', 'views'])->get();
            if ($properties->isEmpty()) {
                Log::error('No sold properties found for this user.', ['exception' => 'No properties found.']);
                return sendResponse(false, 'No sold properties found for this user.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Sold properties retrieved successfully.', PropertyResource::collection($properties), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to retrieve sold properties: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to retrieve sold properties.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function expired(): JsonResponse
    {
        try {
            $properties = $this->propertyService->getProperties()->self()->expired()->with(['primaryImage', 'images', 'views'])->get();
            if ($properties->isEmpty()) {
                Log::error('No expired properties found for this user.', ['exception' => 'No properties found.']);
                return sendResponse(false, 'No expired properties found for this user.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Expired properties retrieved successfully.', PropertyResource::collection($properties), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to retrieve expired properties: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to retrieve expired properties.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleted(): JsonResponse
    {
        try {
            $properties = $this->propertyService->getProperties()->self()->deleted()->with(['primaryImage', 'images', 'views'])->get();
            if ($properties->isEmpty()) {
                Log::error('No deleted properties found for this user.', ['exception' => 'No properties found.']);
                return sendResponse(false, 'No deleted properties found for this user.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Deleted properties retrieved successfully.', PropertyResource::collection($properties), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to retrieve deleted properties: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to retrieve deleted properties.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function myAds(): JsonResponse
    {
        try {
            $properties = $this->propertyService->getProperties()
                ->where([
                    ['user_id', Auth::id()],
                    ['status', '!=', Property::STATUS_DELETED],
                    ['status', '!=', Property::STATUS_ARCHIVE]
                ])->with(['primaryImage', 'images', 'views'])->get();
            if ($properties->isEmpty()) {
                Log::error('No active ads found for this user.', ['exception' => 'No properties found.']);
                return sendResponse(false, 'No active ads found for this user.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'User\'s active ads retrieved successfully.', PropertyResource::collection($properties), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to retrieve user\'s active ads: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to retrieve user\'s active ads.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function myArchivedAds(): JsonResponse
    {
        try {
            $properties = $this->propertyService->getProperties()->self()->whereIn('status', [Property::STATUS_ARCHIVE, Property::STATUS_DELETED])->with(['primaryImage', 'images', 'views'])->get();
            if ($properties->isEmpty()) {
                Log::error('No archived ads found for this user.', ['exception' => 'No properties found.']);
                return sendResponse(false, 'No archived ads found for this user.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'User\'s archived ads retrieved successfully.', PropertyResource::collection($properties), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to retrieve user\'s archived ads: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to retrieve user\'s archived ads.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function myProperty($id): JsonResponse
    {
        try {
            $property = $this->propertyService->getProperty(encrypt($id))->self()->first();
            if (!$property) {
                Log::error('Property not found.', ['exception' => 'Property not found.']);
                return sendResponse(false, 'Property not found.', null, Response::HTTP_NOT_FOUND);
            }
            $property->load(['primaryImage', 'images', 'views']);
            return sendResponse(true, 'Property retrieved successfully.', new PropertyResource($property), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to retrieve property: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to retrieve property.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(PropertyCreateRequest $request): JsonResponse
    {
        try {
            // dd($request->all());
            $validated = $request->validated();
            $property = $this->propertyService->createProperty($validated);
            $primary_file = $validated['primary_file'] ? $request->file('primary_file') : null;
            $files = isset($validated['files']) && is_array($validated['files']) && $request->hasFile('files') ? $request->file('files') : [];
            $this->propertyService->syncPropertyImages($property, $primary_file, $files);
            $property->load(['user']);
            if ($property?->user?->fcm_token != null) {
                $this->firebaseNotificationService->sendToDevice($property->user->fcm_token, 'Property Created', 'A new property has been created.');
            }

            return sendResponse(true, 'Property created successfully.', null, Response::HTTP_CREATED);
        } catch (Throwable $error) {
            Log::error('Failed to create property: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to create property.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(PropertyUpdateRequest $request, $id): JsonResponse
    {
        try {
            $property = $this->propertyService->getProperty(encrypt($id))
                ->where([
                    ['user_id', Auth::id()],
                    ['status', '!=', Property::STATUS_DELETED],
                    ['status', '!=', Property::STATUS_ARCHIVE]
                ])
                ->first();
            if (!$property) {
                return sendResponse(false, 'Property not found.', null, Response::HTTP_NOT_FOUND);
            }
            $validated = $request->validated();
            $property = $this->propertyService->updateProperty($property, $validated);
            $primary_file = isset($validated['primary_file']) ? ($validated['primary_file'] ? $request->file('primary_file') : null) : null;
            $files =  isset($validated['files']) ? ($validated['files'] ? $request->file('files') : []) : [];
            $this->propertyService->syncPropertyImages($property, $primary_file, $files);

            $property->load(['user']);
            if ($property?->user?->fcm_token != null) {
                $this->firebaseNotificationService->sendToDevice($property->user->fcm_token, 'Property Updated', 'A property has been updated.');
            }
            return sendResponse(true, 'Property updated successfully.', null, Response::HTTP_OK); // Changed to HTTP_OK for update
        } catch (Throwable $error) {
            Log::error('Failed to update property: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to update property.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggleFeature($id): JsonResponse
    {
        try {
            $property = $this->propertyService->getProperty(encrypt($id))
                ->where([
                    ['user_id', Auth::id()],
                    ['status', '!=', Property::STATUS_DELETED],
                    ['status', '!=', Property::STATUS_ARCHIVE]
                ])
                ->first();
            if (!$property) {
                return sendResponse(false, 'Property not found.', null, Response::HTTP_NOT_FOUND);
            }
            $property->is_featured = !$property->is_featured;
            $property->save();
            return sendResponse(true, 'Property updated successfully.', null, Response::HTTP_OK); // Changed to HTTP_OK for update
        } catch (Throwable $error) {
            Log::error('Failed to update property: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to update property.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $property = $this->propertyService->getProperty(encrypt($id))->self()->where('status', '!=', Property::STATUS_DELETED)->first();
            if (!$property) {
                return sendResponse(false, 'Property not found.', null, Response::HTTP_NOT_FOUND);
            }
            $this->propertyService->apiDelete($property);
            return sendResponse(true, 'Property deleted successfully.', null, Response::HTTP_OK); // Changed to HTTP_OK for delete
        } catch (Throwable $error) {
            Log::error('Failed to delete property: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to delete property.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function makeArchive($id): JsonResponse
    {
        try {
            $property = $this->propertyService->getProperty(encrypt($id))->self()->where('status', '!=', Property::STATUS_ARCHIVE)->first();
            if (!$property) {
                return sendResponse(false, 'Property not found.', null, Response::HTTP_NOT_FOUND);
            }
            $this->propertyService->apiArchive($property);
            return sendResponse(true, 'Property archived successfully.', null, Response::HTTP_OK); // Changed to HTTP_OK for archive
        } catch (Throwable $error) {
            Log::error('Failed to archive property: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to archive property.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function renew($id): JsonResponse
    {
        try {
            $property = $this->propertyService->getProperty(encrypt($id))->self()->archived()->first();
            if (!$property) {
                return sendResponse(false, 'Property not found.', null, Response::HTTP_NOT_FOUND);
            }
            $this->propertyService->apiRenew($property);
            return sendResponse(true, 'Property renewed successfully.', null, Response::HTTP_OK); // Changed to HTTP_OK for renew
        } catch (Throwable $error) {
            Log::error('Failed to renew property: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to renew property.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function permanentDestroy($id): JsonResponse
    {
        try {
            $property = $this->propertyService->getProperty(encrypt($id))->self()->deleted()->first();
            if (!$property) {
                return sendResponse(false, 'Property not found.', null, Response::HTTP_NOT_FOUND);
            }
            $this->propertyService->apiPermanentDelete($property);
            return sendResponse(true, 'Property deleted successfully.', null, Response::HTTP_OK); // Changed to HTTP_OK for delete
        } catch (Throwable $error) {
            Log::error('Failed to delete property: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to delete property.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function renewProperty(Request $request, $id): JsonResponse
    {

        try {
            $message = '';
            $statusCode = Response::HTTP_OK;

            DB::transaction(function () use ($id, &$message) {

                $property = $this->propertyService->getRenewProperty(encrypt($id), true);

                if ($property->status === Property::STATUS_ARCHIVE) {
                    $this->propertyService->changePropertyStatus($property, Property::STATUS_OPEN);
                    $message = 'Archived property renewed and set to open successfully.';
                } elseif ($property->onlyTrashed() || $property->trashed()) {
                    $this->propertyService->restore(encrypt($id));
                    $this->propertyService->changePropertyStatus($property, Property::STATUS_PENDING);
                    $message = 'Deleted property renewed and set to pending successfully.';
                } else {
                    throw new \Exception('Property is not deleted or archived, cannot renew.');
                }
            });
            return sendResponse(true, $message, null, $statusCode);
        } catch (ModelNotFoundException $error) {
            return sendResponse(false, 'Property not found.', null, Response::HTTP_NOT_FOUND);
        } catch (Throwable $error) {
            Log::error('Failed to renew property: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to renew property. ' . $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
