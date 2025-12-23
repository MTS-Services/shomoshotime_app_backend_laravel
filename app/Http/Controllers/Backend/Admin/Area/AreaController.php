<?php

namespace App\Http\Controllers\Backend\Admin\Area;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Area\AreaRequest;
use App\Http\Traits\AuditRelationTraits;
use App\Services\Area\AreaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AreaController extends Controller
{
    use AuditRelationTraits;

    protected function redirectIndex(): RedirectResponse
    {
        return redirect()->route('area.index');
    }

    protected function redirectTrashed(): RedirectResponse
    {
        return redirect()->route('area.trash');
    }

    protected AreaService $areaService;

    public function __construct(AreaService $areaService)
    {
        $this->areaService = $areaService;
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
            $query = $this->areaService->getAreas();
            return DataTables::eloquent($query)
                ->editColumn('status', fn($area) => "<span class='badge badge-soft {$area->status_color}'>{$area->status_label}</span>")
                ->editColumn('created_by', fn($area) => $this->creater_name($area))
                ->editColumn('created_at', fn($area) => $area->created_at_formatted)
                ->editColumn('action', fn($area) => view('components.admin.action-buttons', ['menuItems' => $this->menuItems($area)])->render())
                ->rawColumns(['status', 'created_by', 'created_at', 'action'])
                ->make(true);
        }
        return view('backend.admin.area.index');
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
                'routeName' => 'area.edit',
                'params' => [encrypt($model->id)],
                'label' => 'Edit',
            ],
            [
                'routeName' => 'area.status',
                'params' => [encrypt($model->id)],
                'label' => $model->status_btn_label,
            ],


            [
                'routeName' => 'area.destroy',
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
        return view('backend.admin.area.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AreaRequest $request)
    {
        try {
            $validated = $request->validated();
            $this->areaService->createArea($validated);
            session()->flash('success', 'Area created successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Area create failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $data = $this->areaService->getArea($id);
        $data['creater_name'] = $this->creater_name($data);
        $data['updater_name'] = $this->updater_name($data);
        return response()->json($data);
    }

    public function status(string $id)
    {
        $area = $this->areaService->getArea($id);
        $this->areaService->toggleStatus($area);
        session()->flash('success', 'Area status updated successfully!');
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['area'] = $this->areaService->getArea($id);
        return view('backend.admin.area.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AreaRequest $request, string $id)
    {
        try {
            $area = $this->areaService->getArea($id);

            $validated = $request->validated();
            $this->areaService->updateArea($area, $validated);
            session()->flash('success', 'Area updated successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Area update failed!');
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
            $area = $this->areaService->getArea($id);
            $this->areaService->delete($area);
            session()->flash('success', 'Area deleted successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Area delete failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->areaService->getAreas()->onlyTrashed();

            return DataTables::eloquent($query)
                ->editColumn('deleted_by', fn($area) => $this->deleter_name($area))
                ->editColumn('deleted_at', fn($area) => $area->deleted_at_formatted)
                ->editColumn('action', fn($area) => view('components.admin.action-buttons', [
                    'menuItems' => $this->trashedMenuItems($area),
                ])->render())
                ->rawColumns(['deleted_by', 'deleted_at', 'action'])
                ->make(true);
        }

        return view('backend.admin.area.trash');
    }


    protected function trashedMenuItems($model): array
    {
        return [
            [
                'routeName' => 'area.restore',
                'params' => [encrypt($model->id)],
                'label' => 'Restore',
            ],
            [
                'routeName' => 'area.permanent-delete',
                'params' => [encrypt($model->id)],
                'label' => 'Permanent Delete',
                'p-delete' => true,
            ]

        ];
    }

    public function restore(string $id)
    {
        try {
            $this->areaService->restore($id);
            session()->flash('success', "Area restored successfully");
        } catch (\Throwable $e) {
            session()->flash('Area restore failed');
            throw $e;
        }
        return $this->redirectTrashed();
    }

    public function permanentDelete(string $id)
    {
        try {
            $this->areaService->permanentDelete($id);
            session()->flash('success', "Area permanently deleted successfully");
        } catch (\Throwable $e) {
            session()->flash('Area permanent delete failed');
            throw $e;
        }
        return $this->redirectTrashed();
    }
}
