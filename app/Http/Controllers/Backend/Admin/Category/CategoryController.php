<?php

namespace App\Http\Controllers\Backend\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\CategoryRequest;
use App\Http\Traits\AuditRelationTraits;
use App\Services\Category\CategoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    use AuditRelationTraits;

    protected function redirectIndex(): RedirectResponse
    {
        return redirect()->route('category.index');
    }

    protected function redirectTrashed(): RedirectResponse
    {
        return redirect()->route('category.trash');
    }

    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
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
            $query = $this->categoryService->getCategories();
            return DataTables::eloquent($query)
                ->editColumn('created_by', fn($category) => $this->creater_name($category))
                ->editColumn('created_at', fn($category) => $category->created_at_formatted)
                ->editColumn('action', fn($category) => view('components.admin.action-buttons', ['menuItems' => $this->menuItems($category)])->render())
                ->rawColumns(['created_by', 'created_at', 'action'])
                ->make(true);
        }
        return view('backend.admin.category.index');
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
                'routeName' => 'category.edit',
                'params' => [encrypt($model->id)],
                'label' => 'Edit',
            ],
            [
                'routeName' => 'category.destroy',
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
        return view('backend.admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        try {
            $validated = $request->validated();
            $this->categoryService->createCategory($validated);
            session()->flash('success', 'Category created successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Category create failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }
    public function edit(string $id)
    {
        $data['category'] = $this->categoryService->getCategory($id);
        return view('backend.admin.category.edit', $data);
    }
    public function update(CategoryRequest $request, string $id)
    {
        try {
            $category = $this->categoryService->getCategory($id);

            $validated = $request->validated();
            $this->categoryService->updateCategory($category, $validated);
            session()->flash('success', 'Category updated successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Category update failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $data = $this->categoryService->getCategory($id);
        $data['creater_name'] = $this->creater_name($data);
        $data['updater_name'] = $this->updater_name($data);
        return response()->json($data);
    }


    public function destroy(string $id)
    {
        try {
            $category = $this->categoryService->getCategory($id);
            $this->categoryService->delete($category);
            session()->flash('success', 'Category deleted successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Category delete failed!');
            throw $e;
        }
        return $this->redirectIndex();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->categoryService->getCategories()->onlyTrashed();

            return DataTables::eloquent($query)
                ->editColumn('deleted_by', fn($category) => $this->deleter_name($category))
                ->editColumn('deleted_at', fn($category) => $category->deleted_at_formatted)
                ->editColumn('action', fn($category) => view('components.admin.action-buttons', [
                    'menuItems' => $this->trashedMenuItems($category),
                ])->render())
                ->rawColumns(['deleted_by', 'deleted_at', 'action'])
                ->make(true);
        }

        return view('backend.admin.category.trash');
    }


    protected function trashedMenuItems($model): array
    {
        return [
            [
                'routeName' => 'category.restore',
                'params' => [encrypt($model->id)],
                'label' => 'Restore',
            ],
            [
                'routeName' => 'category.permanent-delete',
                'params' => [encrypt($model->id)],
                'label' => 'Permanent Delete',
                'p-delete' => true,
            ]

        ];
    }

    public function restore(string $id)
    {
        try {
            $this->categoryService->restore($id);
            session()->flash('success', "Category restored successfully");
        } catch (\Throwable $e) {
            session()->flash('Category restore failed');
            throw $e;
        }
        return $this->redirectTrashed();
    }

    public function permanentDelete(string $id)
    {
        try {
            $this->categoryService->permanentDelete($id);
            session()->flash('success', "Category permanently deleted successfully");
        } catch (\Throwable $e) {
            session()->flash('Category permanent delete failed');
            throw $e;
        }
        return $this->redirectTrashed();
    }
}
