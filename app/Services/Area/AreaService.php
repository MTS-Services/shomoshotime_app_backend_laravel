<?php

namespace App\Services\Area;

use App\Http\Traits\FileManagementTrait;
use App\Models\Area;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AreaService
{
    use FileManagementTrait;

    // Backend Services
    public function getAreas($orderBy = 'sort_order', $order = 'asc')
    {
        return Area::withCount('properties')->orderBy($orderBy, $order)->latest();
    }
    public function getArea(string $encryptedId): Area|Collection
    {
        return Area::withCount('properties')->findOrFail(decrypt($encryptedId));
    }
    public function getDeletedArea(string $encryptedId): Area|Collection
    {
        return Area::onlyTrashed()->findOrFail(decrypt($encryptedId));
    }

    public function createArea(array $data): Area
    {
        return DB::transaction(function () use ($data) {
           
         
            $data['created_by'] = user()->id;
            $area = Area::create($data);
            return $area;
        });
    }

    public function updateArea(Area $area, array $data): Area
    {
        return DB::transaction(function () use ($area, $data) {
           
          
            $data['updated_by'] = user()->id;
            $area->update($data);
            return $area;
        });
    }

    public function delete(Area $area): void
    {
        $area->update(['deleted_by' => user()->id]);
        $area->delete();
    }

    public function restore(string $encryptedId): void
    {
        $area = $this->getDeletedArea($encryptedId);
        $area->update(['updated_by' => user()->id]);
        $area->restore();
    }

    public function permanentDelete(string $encryptedId): void
    {
        $area = $this->getDeletedArea($encryptedId);
        $area->forceDelete();
    }

    public function toggleStatus(Area $area): void
    {
        $area->update([
            'status' => !$area->status,
            'updated_by' => user()->id
        ]);
    }
}
