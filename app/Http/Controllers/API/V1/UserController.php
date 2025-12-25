<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\UserResource;
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
            if (!$user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            if (!$user->isAdmin()) {
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

            return sendResponse(true, 'Users data fetched successfully.', UserResource::collection($users), Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: ' . $e->getMessage());
            return sendResponse(false, 'Something went wrong.' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
