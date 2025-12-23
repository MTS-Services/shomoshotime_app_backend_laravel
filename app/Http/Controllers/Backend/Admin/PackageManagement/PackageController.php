<?php

namespace App\Http\Controllers\Backend\Admin\PackageManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageManagement\PackageRequest;
use App\Http\Traits\AuditRelationTraits;
use App\Services\PackageManagement\PackageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PackageController extends Controller
{
    use AuditRelationTraits;

    protected function redirectIndex(): RedirectResponse
    {
        return redirect()->route('pam.package.index');
    }

    protected function redirectTrashed(): RedirectResponse
    {
        return redirect()->route('pam.package.trash');
    }

    protected PackageService $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
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
            $query = $this->packageService->getPackages();
            return DataTables::eloquent($query)
                ->editColumn('created_by', fn($package) => $this->creater_name($package))
                ->editColumn('created_at', fn($package) => $package->created_at_formatted)
                ->editColumn('price', fn($package) => $package->price . " KWD")
                ->editColumn('status', fn($package) => '<span class="badge badge-soft badge-' . $package->status_color . '">' . $package->status_label . '</span>')
                ->editColumn('action', fn($package) => view('components.admin.action-buttons', ['menuItems' => $this->menuItems($package)])->render())
                ->rawColumns(['created_by', 'created_at', 'price', 'status', 'action'])
                ->make(true);
        }
        return view('backend.admin.package-management.package.index');
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
                'routeName' => 'pam.package.status',
                'params' => [encrypt($model->id)],
                'label' => $model->status == 1 ? 'Inactive' : 'Active',
            ],
            [
                'routeName' => 'pam.package.edit',
                'params' => [encrypt($model->id)],
                'label' => 'Edit',
            ],
            [
                'routeName' => 'pam.package.destroy',
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
        return view('backend.admin.package-management.package.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PackageRequest $request)
    {
        try {
            $validated = $request->validated();
            $this->packageService->createPackage($validated);
            session()->flash('success', 'Package created successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Package create failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }
    public function edit(string $id)
    {
        $data['package'] = $this->packageService->getPackage($id);
        return view('backend.admin.package-management.package.edit', $data);
    }
    public function update(PackageRequest $request, string $id)
    {
        try {
            $package = $this->packageService->getPackage($id);

            $validated = $request->validated();
            $this->packageService->updatePackage($package, $validated);
            session()->flash('success', 'Package updated successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Package update failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $data = $this->packageService->getPackage($id);
        $data['creater_name'] = $this->creater_name($data);
        $data['updater_name'] = $this->updater_name($data);
        return response()->json($data);
    }

    public function status(string $id)
    {
        try {
            $package = $this->packageService->getPackage($id);
            $this->packageService->toggleStatus($package);
            session()->flash('success', 'Package status updated successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Package status update failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }


    public function destroy(string $id)
    {
        try {
            $package = $this->packageService->getPackage($id);
            $this->packageService->delete($package);
            session()->flash('success', 'Package deleted successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Package delete failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->packageService->getPackages()->onlyTrashed();

            return DataTables::eloquent($query)
                ->editColumn('deleted_by', fn($package) => $this->deleter_name($package))
                ->editColumn('deleted_at', fn($package) => $package->deleted_at_formatted)
                ->editColumn('price', fn($package) => $package->price . " KWD")
                ->editColumn('status', fn($package) => '<span class="badge badge-soft badge-' . $package->status_color . '">' . $package->status_label . '</span>')
                ->editColumn('action', fn($package) => view('components.admin.action-buttons', [
                    'menuItems' => $this->trashedMenuItems($package),
                ])->render())
                ->rawColumns(['deleted_by', 'deleted_at', 'price', 'status', 'action'])
                ->make(true);
        }

        return view('backend.admin.package-management.package.trash');
    }


    protected function trashedMenuItems($model): array
    {
        return [
            [
                'routeName' => 'pam.package.restore',
                'params' => [encrypt($model->id)],
                'label' => 'Restore',
            ],
            [
                'routeName' => 'pam.package.permanent-delete',
                'params' => [encrypt($model->id)],
                'label' => 'Permanent Delete',
                'p-delete' => true,
            ]

        ];
    }

    public function restore(string $id)
    {
        try {
            $this->packageService->restore($id);
            session()->flash('success', "Package restored successfully");
        } catch (\Throwable $e) {
            session()->flash('error', 'Package restore failed');
            throw $e;
        }
        return $this->redirectTrashed();
    }

    public function permanentDelete(string $id)
    {
        try {
            $this->packageService->permanentDelete($id);
            session()->flash('success', "Package permanently deleted successfully");
        } catch (\Throwable $e) {
            session()->flash('error', 'Package permanent delete failed');
            throw $e;
        }
        return $this->redirectTrashed();
    }
}
