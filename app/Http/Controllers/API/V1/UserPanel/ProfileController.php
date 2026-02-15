<?php

namespace App\Http\Controllers\API\V1\UserPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\UserResource;
use App\Services\UserManagement\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProfileController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function getProfile(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }
            $profile = $this->service->getUser($user->id);

            return sendResponse(true, 'Profile data fetched successfully.', new UserResource($profile), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,'.$user->id,
                'password' => 'sometimes|nullable|string|min:8|confirmed',
                'file' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return sendResponse(false, $validator->errors()->first(), null, Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            $data = $validator->validated();

            $image = $request->file('file') ?? null;

            $profile = $this->service->updateUser($user, $data, $image);

            return sendResponse(true, 'Profile data updated successfully.', new UserResource($profile), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function subscriptionCheck(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
        }
        if ($user->is_premium) {
            return sendResponse(true, 'User has an active premium subscription.', ['is_premium' => true], Response::HTTP_OK);
        } else {
            return sendResponse(true, 'User does not have an active premium subscription.', ['is_premium' => false], Response::HTTP_OK);
        }
    }

    public function deleteAccount(Request $request)
    {
        try {
            $user = $request->user();

            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            if (method_exists($user, 'userDevices')) {
                $user->userDevices()->delete();
            }

            $this->service->deleteUser($user);

            return sendResponse(true, 'Account deleted successfully. Please login again if needed.', null, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Delete Account Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
