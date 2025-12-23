<?php

namespace App\Services\PropertyManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PropertyTypeService
{
    use FileManagementTrait;

    public function getPropertyTypes($orderBy = 'sort_order', $order = 'asc')
    {
        return PropertyType::orderBy($orderBy, $order)->latest();
    }

    public function getPropertyType(string $encryptedId): PropertyType|Collection
    {
        return PropertyType::findOrFail(decrypt($encryptedId));
    }

    public function getDeletedPropertyType(string $encryptedId): PropertyType|Collection
    {
        return PropertyType::onlyTrashed()->findOrFail(decrypt($encryptedId));
    }

    public function createPropertyType(array $data): PropertyType
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = user()->id;
            $propertyType = PropertyType::create($data);
            return $propertyType;
        });
    }

    public function updatePropertyType(PropertyType $propertyType, array $data): PropertyType
    {
        return DB::transaction(function () use ($propertyType, $data) {
            $data['updated_by'] = user()->id;
            $propertyType->update($data);
            return $propertyType;
        });
    }

    public function delete(PropertyType $propertyType): void
    {
        $propertyType->update(['deleted_by' => user()->id]);
        $propertyType->delete();
    }

    public function restore(string $encryptedId): void
    {
        $propertyType = $this->getDeletedPropertyType($encryptedId);
        $propertyType->update(['updated_by' => user()->id]);
        $propertyType->restore();
    }

    public function permanentDelete(string $encryptedId): void
    {
        $propertyType = $this->getDeletedPropertyType($encryptedId);
        $propertyType->forceDelete();
    }

    public function toggleStatus(PropertyType $propertyType): void
    {
        $propertyType->update([
            'status' => !$propertyType->status,
            'updated_by' => user()->id
        ]);
    }
}


