<?php

namespace App\Http\Controllers\Backend\Admin\PropertyManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PropertyManagement\PropertyTypeRequest;
use App\Http\Traits\AuditRelationTraits;
use App\Services\PropertyManagement\PropertyTypeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PropertyTypeController extends Controller
{
    use AuditRelationTraits;

    protected function redirectIndex(): RedirectResponse
    {
        return redirect()->route('pm.property-type.index');
    }

    protected function redirectTrashed(): RedirectResponse
    {
        return redirect()->route('pm.property-type.trash');
    }

    protected PropertyTypeService $propertyTypeService;

    public function __construct(PropertyTypeService $propertyTypeService)
    {
        $this->propertyTypeService = $propertyTypeService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->propertyTypeService->getPropertyTypes();
            return DataTables::eloquent($query)
                ->editColumn('created_by', fn($type) => $this->creater_name($type))
                ->editColumn('created_at', fn($type) => $type->created_at_formatted)
                ->editColumn('action', fn($type) => view('components.admin.action-buttons', ['menuItems' => $this->menuItems($type)])->render())
                ->rawColumns([ 'created_by', 'created_at', 'action'])
                ->make(true);
        }
        return view('backend.admin.property-managment.property-type.index');
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
                'routeName' => 'pm.property-type.edit',
                'params' => [encrypt($model->id)],
                'label' => 'Edit',
            ],
            [
                'routeName' => 'pm.property-type.destroy',
                'params' => [encrypt($model->id)],
                'label' => 'Delete',
                'delete' => true,
            ]
        ];
    }

    public function create(): View
    {
        return view('backend.admin.property-managment.property-type.create');
    }

    public function store(PropertyTypeRequest $request)
    {
        try {
            $validated = $request->validated();
            $this->propertyTypeService->createPropertyType($validated);
            session()->flash('success', 'Property type created successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Property type create failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    public function show(Request $request, string $id)
    {
        $data = $this->propertyTypeService->getPropertyType($id);
        $data['creater_name'] = $this->creater_name($data);
        $data['updater_name'] = $this->updater_name($data);
        return response()->json($data);
    }

    public function edit(string $id)
    {
        $data['property_type'] = $this->propertyTypeService->getPropertyType($id);
        return view('backend.admin.property-managment.property-type.edit', $data);
    }

    public function update(PropertyTypeRequest $request, string $id)
    {
        try {
            $propertyType = $this->propertyTypeService->getPropertyType($id);

            $validated = $request->validated();
            $this->propertyTypeService->updatePropertyType($propertyType, $validated);
            session()->flash('success', 'Property type updated successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Property type update failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    public function destroy(string $id)
    {
        try {
            $propertyType = $this->propertyTypeService->getPropertyType($id);
            $this->propertyTypeService->delete($propertyType);
            session()->flash('success', 'Property type deleted successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Property type delete failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->propertyTypeService->getPropertyTypes()->onlyTrashed();

            return DataTables::eloquent($query)
                ->editColumn('deleted_by', fn($type) => $this->deleter_name($type))
                ->editColumn('deleted_at', fn($type) => $type->deleted_at_formatted)
                ->editColumn('action', fn($type) => view('components.admin.action-buttons', [
                    'menuItems' => $this->trashedMenuItems($type),
                ])->render())
                ->rawColumns(['deleted_by', 'deleted_at', 'action'])
                ->make(true);
        }

        return view('backend.admin.property-managment.property-type.trash');
    }

    protected function trashedMenuItems($model): array
    {
        return [
            [
                'routeName' => 'pm.property-type.restore',
                'params' => [encrypt($model->id)],
                'label' => 'Restore',
            ],
            [
                'routeName' => 'pm.property-type.permanent-delete',
                'params' => [encrypt($model->id)],
                'label' => 'Permanent Delete',
                'p-delete' => true,
            ]
        ];
    }

    public function restore(string $id)
    {
        try {
            $this->propertyTypeService->restore($id);
            session()->flash('success', "Property type restored successfully");
        } catch (\Throwable $e) {
            session()->flash('Property type restore failed');
            throw $e;
        }
        return $this->redirectTrashed();
    }

    public function permanentDelete(string $id)
    {
        try {
            $this->propertyTypeService->permanentDelete($id);
            session()->flash('success', "Property type permanently deleted successfully");
        } catch (\Throwable $e) {
            session()->flash('Property type permanent delete failed');
            throw $e;
        }
        return $this->redirectTrashed();
    }
}

