<?php

namespace App\Http\Controllers\Backend\Admin\UserManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserManagement\UserRequest;
use App\Http\Traits\AuditRelationTraits;
use App\Models\User;
use App\Services\UserManagement\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    use AuditRelationTraits;

    protected function redirectIndex(): RedirectResponse
    {
        return redirect()->route('am.user.index');
    }

    protected function redirectTrashed(): RedirectResponse
    {
        return redirect()->route('am.user.trash');
    }

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->userService->getUsers()->notAdmin();
            return DataTables::eloquent($query)
                ->editColumn('email_verified_at', fn($user) => "<span class='badge badge-soft {$user->verify_color}'>{$user->verify_label}</span>")
                ->editColumn('status', fn($user) => "<span class='badge badge-soft {$user->status_color}'>{$user->status_label}</span>")
                ->editColumn('created_by', fn($user) => $this->creater_name($user))
                ->editColumn('created_at', fn($user) => $user->created_at_formatted)
                ->editColumn('action', fn($user) => view('components.admin.action-buttons', ['menuItems' => $this->menuItems($user)])->render())
                ->rawColumns(['status', 'email_verified_at', 'created_by', 'created_at', 'action'])
                ->make(true);
        }
        return view('backend.admin.user-management.user.index');
    }

    protected function menuItems($model): array
    {
        return [
            [
                'routeName' => 'javascript:void(0)',
                'data-id' => encrypt($model->id),
                'className' => 'view',
                'label' => 'Details',
            ],
            [
                'routeName' => 'am.user.status',
                'params' => [encrypt($model->id)],
                'label' => $model->status_btn_label,
            ],
            [
                'routeName' => 'am.user.edit',
                'params' => [encrypt($model->id)],
                'label' => 'Edit',
            ],

            [
                'routeName' => 'am.user.destroy',
                'params' => [encrypt($model->id)],
                'label' => 'Delete',
                'delete' => true,
            ]

        ];
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('backend.admin.user-management.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        try {
            $validated = $request->validated();
            $file = $request->validated('image') && $request->hasFile('image') ? $request->file('image') : null;
            $this->userService->createUser($validated, $file);
            session()->flash('success', 'User created successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'User create failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $data = $this->userService->getUser($id);
        $data['creater_name'] = $this->creater_name($data);
        $data['updater_name'] = $this->updater_name($data);
        return response()->json($data);
    }

    public function status(string $id)
    {
        $user = $this->userService->getUser($id);
        $this->userService->toggleStatus($user);
        session()->flash('success', 'User status updated successfully!');
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['user'] = $this->userService->getUser($id);
        return view('backend.admin.user-management.user.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        try {
            $user = $this->userService->getUser($id);

            $validated = $request->validated();
            $file = $request->validated('image') && $request->hasFile('image') ? $request->file('image') : null;
            $this->userService->updateUser($user, $validated, $file);
            session()->flash('success', 'User updated successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'User update failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = $this->userService->getUser($id);
            $this->userService->delete($user);
            session()->flash('success', 'User deleted successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'User delete failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->userService->getUsers()->onlyTrashed()->notadmin();

            return DataTables::eloquent($query)
                ->editColumn('deleted_by', fn($user) => $this->deleter_name($user))
                ->editColumn('deleted_at', fn($user) => $user->deleted_at_formatted)
                ->editColumn('action', fn($user) => view('components.admin.action-buttons', [
                    'menuItems' => $this->trashedMenuItems($user),
                ])->render())
                ->rawColumns(['deleted_by', 'deleted_at', 'action'])
                ->make(true);
        }

        return view('backend.admin.user-management.user.trash');
    }


    protected function trashedMenuItems($model): array
    {
        return [
            [
                'routeName' => 'am.user.restore',
                'params' => [encrypt($model->id)],
                'label' => 'Restore',
            ],
            [
                'routeName' => 'am.user.permanent-delete',
                'params' => [encrypt($model->id)],
                'label' => 'Permanent Delete',
                'p-delete' => true,
            ]

        ];
    }

    public function restore(string $id)
    {
        try {
            $this->userService->restore($id);
            session()->flash('success', "User restored successfully");
        } catch (\Throwable $e) {
            session()->flash('User restore failed');
            throw $e;
        }
        return $this->redirectTrashed();
    }

    public function permanentDelete(string $id)
    {
        try {
            $this->userService->permanentDelete($id);
            session()->flash('success', "User permanently deleted successfully");
        } catch (\Throwable $e) {
            session()->flash('User permanent delete failed');
            throw $e;
        }
        return $this->redirectTrashed();
    }
}
