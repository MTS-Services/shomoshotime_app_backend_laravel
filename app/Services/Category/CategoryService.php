<?php

namespace App\Services\Category;

use App\Http\Traits\FileManagementTrait;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    use FileManagementTrait;

    public function getCategories($orderBy = 'sort_order', $order = 'asc')
    {
        return Category::orderBy($orderBy, $order)->latest();
    }
    public function getCategory(string $encryptedId): Category|Collection
    {
        return Category::findOrFail(decrypt($encryptedId));
    }
    public function getDeletedCategory(string $encryptedId): Category|Collection
    {
        return Category::onlyTrashed()->findOrFail(decrypt($encryptedId));
    }

    public function createCategory(array $data): Category
    {
        return DB::transaction(function () use ($data) {

            $data['created_by'] = user()->id;
            $category = Category::create($data);
            return $category;
        });
    }

    public function updateCategory(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {


            $data['updated_by'] = user()->id;
            $category->update($data);
            return $category;
        });
    }

    public function delete(Category $category): void
    {
        $category->update(['deleted_by' => user()->id]);
        $category->delete();
    }

    public function restore(string $encryptedId): void
    {
        $category = $this->getDeletedCategory($encryptedId);
        $category->update(['updated_by' => user()->id]);
        $category->restore();
    }

    public function permanentDelete(string $encryptedId): void
    {
        $category = $this->getDeletedCategory($encryptedId);
        $category->forceDelete();
    }

    public function toggleStatus(Category $category): void
    {
        $category->update([
            'status' => !$category->status,
            'updated_by' => user()->id
        ]);
    }
}
