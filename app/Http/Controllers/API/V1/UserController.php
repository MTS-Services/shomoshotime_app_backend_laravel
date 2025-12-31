<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\UserCollection;
use App\Services\UserManagement\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getUsers(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
            }

            $query = $this->userService->getUsers();
            if ($request->has('search')) {
                $searchQuery = $request->input('search');
                $query->whereLike('name', $searchQuery)
                    ->whereLike('email', $searchQuery);
            }
            $users = $query->paginate($request->input('per_page', 10));
            $users->setPageName('page');

            return sendResponse(true, 'Users data fetched successfully.', new UserCollection($users), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function statusChange(Request $request)
    {
        try {

            $user = request()->user();

            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (! $user->isAdmin()) {
                return sendResponse(false, 'Admin access required', null, Response::HTTP_UNAUTHORIZED);
            }

            if (empty($request->id)) {
                return sendResponse(false, 'User id required', null, Response::HTTP_UNAUTHORIZED);
            }
            if (! isset($request->status)) {
                return sendResponse(false, 'Status field is required', null, Response::HTTP_BAD_REQUEST);
            }

            $user = $this->userService->getUser($request->id);
            if (! $user) {
                return sendResponse(false, 'User not found', null, Response::HTTP_UNAUTHORIZED);
            }
            $this->userService->statusChange($user, $request->status);

            return sendResponse(true, 'Status changed successfully.', null, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
