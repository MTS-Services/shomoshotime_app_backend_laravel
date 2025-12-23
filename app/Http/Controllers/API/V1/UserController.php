<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\UserManagement\CompanyInformationRequest;
use App\Http\Requests\API\V1\UserManagement\UpdateUserRequest;
use App\Http\Resources\API\V1\UserManagement\UserCompanyInformationResource;
use App\Http\Resources\API\V1\UserManagement\UserProfileResource;
use App\Http\Resources\API\V1\UserManagement\UserResource;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\UserManagement\UserProfileService;
use App\Services\UserManagement\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserController extends Controller
{
    protected UserService $userService;
    protected UserProfileService $userProfileService;

    public function __construct(UserService $userService, UserProfileService $userProfileService)
    {
        $this->userService = $userService;
        $this->userProfileService = $userProfileService;
    }

    /**
     * Get the authenticated user's details.
     */
    public function me(): JsonResponse
    {
        try {
            $user = $this->userService->getAuthenticatedUserWithRelations(['profile', 'companyInformation']);
            return sendResponse(true, 'User details retrieved successfully.', new UserResource($user), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to get authenticated user details: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to get user details', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get the authenticated user's profile information.
     */
    public function profile(): JsonResponse
    {
        try {
            $profile = $this->userService->getAuthenticatedUserProfile();

            if (!$profile) {
                Log::error('Failed to get authenticated user profile.');
                return sendResponse(false, 'User profile not found.', null, Response::HTTP_NOT_FOUND);
            }

            return sendResponse(true, 'User profile retrieved successfully.', new UserProfileResource($profile), Response::HTTP_OK);
        } catch (Throwable $error) {
            // Fix: Wrap the $error object in an array for the context
            Log::error('Failed to get authenticated user profile: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to get user profile', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get the authenticated user's company information.
     */
    public function companyInfo(): JsonResponse
    {
        try {
            $company = $this->userService->getAuthenticatedCompanyInformation();
            if (!$company) {
                Log::error('Failed to get authenticated user company information.');
                return sendResponse(false, 'Company information not found.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Company information retrieved successfully.', new UserCompanyInformationResource($company), Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to get authenticated user company information: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to get company information', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $file = $request->validated('image') && $request->hasFile('image') ? $request->file('image') : null;
                $user = $this->userService->updateUser(request()->user(), $validated, $file);
                $this->userProfileService->updateUserProfile($validated, $user->id);
            });
            return sendResponse(true, 'User updated successfully.', null, Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to update user: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to update user', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeType(Request $request): JsonResponse
    {
        try {
            $user = request()->user();
            if ($user->user_type === User::USER_TYPE_INDIVIDUAL) {
                $user->user_type = User::USER_TYPE_AGENT;
            } elseif ($user->user_type === User::USER_TYPE_AGENT) {
                $user->user_type = User::USER_TYPE_INDIVIDUAL;
            } else {
                return sendResponse(false, 'You are not authorized to change user type.', null, Response::HTTP_UNAUTHORIZED);
            }
            $user->save();
            return sendResponse(true, 'User Type changed successfully.', [
                'user_type' => $user->user_type,
                'user_type_label' => $user->user_type_label
            ], Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to change user: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to change user', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateCompanyInfo(CompanyInformationRequest $request): JsonResponse
    {
        try {
            $user = request()->user();
            if ($user->user_type != User::USER_TYPE_AGENT) {
                return sendResponse(false, 'Before updating company information, you must be an agent.', null, Response::HTTP_UNAUTHORIZED);
            }
            $validated = $request->validated();
            $file = $request->validated('image') && $request->hasFile('image') ? $request->file('image') : null;
            $this->userService->updateCompanyInformation($user, $request->all(), $file);
            return sendResponse(true, 'Company information updated successfully.', null, Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to update company information: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to update company information', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function delete(): JsonResponse
    {
        try {
            $user = request()->user();
         
            $this->userService->delete($user);
            return sendResponse(true, 'User deleted successfully.', null, Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to delete user: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to delete user', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function toggleStatus(): JsonResponse
    {
        try {
            $user = request()->user();
            if (!$user) {
                return sendResponse(false, 'Authenticated user not found.', null, Response::HTTP_UNAUTHORIZED);
            }

            $updatedUser = $this->userService->toggleStatus($user);
            $statusLabel = $updatedUser->status === User::STATUS_ACTIVE ? 'Active' : 'Inactive';

            return sendResponse(true, "Your status has been changed to {$statusLabel}.", [
                'status' => $updatedUser->status,
                'status_label' => $statusLabel,
            ], Response::HTTP_OK);
        } catch (Throwable $error) {
            Log::error('Failed to toggle authenticated user status: ', ['exception' => $error]);
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
