<?php

namespace App\Http\Controllers\Backend\Admin\PropertyManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PropertyManagement\PropertyRequest;
use App\Http\Traits\AuditRelationTraits;
use App\Models\Area;
use App\Models\Property;
use App\Models\PropertyType;
use App\Services\Area\AreaService;
use App\Services\Category\CategoryService;
use App\Services\PropertyManagement\PropertyService;
use App\Services\PropertyManagement\PropertyTypeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

use function Pest\Laravel\get;

class PropertyController extends Controller
{
    use AuditRelationTraits;

    protected function redirectIndex(): RedirectResponse
    {
        return redirect()->route('pm.property.index');
    }

    protected function redirectTrashed(): RedirectResponse
    {
        return redirect()->route('pm.property.trash');
    }

    protected PropertyService $propertyService;
    protected PropertyTypeService $propertyTypeService;
    protected CategoryService $categoryService;
    protected AreaService $areaService;

    public function __construct(PropertyService $propertyService, PropertyTypeService $propertyTypeService, CategoryService $categoryService, AreaService $areaService)
    {
        $this->propertyService = $propertyService;
        $this->propertyTypeService = $propertyTypeService;
        $this->categoryService = $categoryService;
        $this->areaService = $areaService;
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
            $query = $this->propertyService->getProperties();
            return DataTables::eloquent($query)
                ->addColumn('property_type_id', fn($property) => $property->propertyType->name)
                ->addColumn('area_id', fn($property) => $property->area->name)
                ->addColumn('category_id', fn($property) => $property->category->name)
                ->addColumn('status', fn($property) => "<span class='badge badge-soft {$property->status_color}'>{$property->status_label}</span>")
                ->editColumn('created_by', fn($property) => $this->creater_name($property))
                ->editColumn('created_at', fn($property) => $property->created_at_formatted)
                ->editColumn('action', fn($property) => view('components.admin.action-buttons', ['menuItems' => $this->menuItems($property)])->render())
                ->rawColumns(['property_type', 'area', 'category', 'status', 'created_by', 'created_at', 'action'])
                ->make(true);
        }
        return view('backend.admin.property-managment.property.index');
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
                'routeName' => 'pm.property.edit',
                'params' => [encrypt($model->id)],
                'label' => 'Edit',
            ],
            [
                'routeName' => 'pm.property.status',
                'params' => [encrypt($model->id)],
                'label' => $model->status_btn_label,
                'color' => $model->status_btn_color,
            ],
            [
                'routeName' => 'pm.property.destroy',
                'params' => [encrypt($model->id)],
                'label' => 'Delete',
                'delete' => true,
            ]
        ];
    }

    public function create(): View
    {
        $data['propertyTypes'] = $this->propertyTypeService->getPropertyTypes()->get();
        $data['categories'] = $this->categoryService->getCategories()->get();
        $data['areas'] = $this->areaService->getAreas()->get();

        return view('backend.admin.property-managment.property.create', $data);
    }

    public function store(PropertyRequest $request)
    {

        try {
            DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $property = $this->propertyService->createProperty($validated);
                $file = $request->validated('file') && $request->hasFile('file') ? $request->file('file') : null;
                $files = $request->validated('files') && $request->hasFile('files') ? (array) $request->file('files') : [];
                $this->propertyService->imageCreate($property, $file, $files);
            });
            session()->flash('success', 'Property created successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Property create failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    public function show(Request $request, string $id)
    {
        $data = $this->propertyService->getProperty($id)->first();
        $data['creater_name'] = $this->creater_name($data);
        $data['updater_name'] = $this->updater_name($data);
        return response()->json($data);
    }

    public function edit(string $id)
    {

        $data['property'] = $this->propertyService->getProperty($id)->with(['primaryImage', 'nonPrimaryImages'])->first();
        $data['property_types'] = $this->propertyTypeService->getPropertyTypes()->get();
        $data['categories'] = $this->categoryService->getCategories()->get();
        $data['areas'] = $this->areaService->getAreas()->get();
        return view('backend.admin.property-managment.property.edit', $data);
    }

    public function update(PropertyRequest $request, string $id)
    {
        try {

            DB::transaction(function () use ($request, $id) {
                $property = $this->propertyService->getProperty($id)->first();
                $validated = $request->validated();
                $this->propertyService->updateProperty($property, $validated);
                $file = $request->hasFile('file') ? $request->file('file') : null;
                $files = $request->hasFile('files') ? (array) $request->file('files') : [];
                if ($file || !empty($files)) {
                    $this->propertyService->imageUpdate($property, $file, $files);
                }
            });

            session()->flash('success', 'Property updated successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Property update failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }


    public function destroy(string $id)
    {
        try {
            $property = $this->propertyService->getProperty($id)->first();
            $this->propertyService->delete($property);
            session()->flash('success', 'Property deleted successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Property delete failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->propertyService->getProperties()->onlyTrashed();

            return DataTables::eloquent($query)
                ->addColumn('property_type_id', fn($property) => $property->propertyType->name)
                ->addColumn('area_id', fn($property) => $property->area->name)
                ->addColumn('category_id', fn($property) => $property->category->name)
                ->editColumn('deleted_by', fn($property) => $this->deleter_name($property))
                ->editColumn('deleted_at', fn($property) => $property->deleted_at_formatted)
                ->editColumn('action', fn($property) => view('components.admin.action-buttons', [
                    'menuItems' => $this->trashedMenuItems($property),
                ])->render())
                ->rawColumns(['deleted_by', 'property_type_id', 'area_id', 'category_id', 'deleted_at', 'action'])
                ->make(true);
        }

        return view('backend.admin.property-managment.property.trash');
    }

    protected function trashedMenuItems($model): array
    {
        return [
            [
                'routeName' => 'pm.property.restore',
                'params' => [encrypt($model->id)],
                'label' => 'Restore',
            ],
            [
                'routeName' => 'pm.property.permanent-delete',
                'params' => [encrypt($model->id)],
                'label' => 'Permanent Delete',
                'p-delete' => true,
            ]
        ];
    }

    public function restore(string $id)
    {
        try {
            $this->propertyService->restore($id);
            session()->flash('success', "Property restored successfully");
        } catch (\Throwable $e) {
            session()->flash('Property restore failed');
            throw $e;
        }
        return $this->redirectTrashed();
    }

    public function permanentDelete(string $id)
    {
        try {
            $this->propertyService->permanentDelete($id);
            session()->flash('success', "Property permanently deleted successfully");
        } catch (\Throwable $e) {
            session()->flash('Property permanent delete failed');
            throw $e;
        }
        return $this->redirectTrashed();
    }

    public function status(string $id)
    {
        try {
            $property = $this->propertyService->getProperty($id)->first();
            $this->propertyService->toggleStatus($property);
            session()->flash('success', 'Property status updated successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Property status update failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }
}
