<?php

namespace App\Services\PackageManagement;

use App\Models\Package;
use Illuminate\Database\Eloquent\Collection;

class PackageService
{
    public function getPackages($orderBy = 'sort_order', $order = 'asc')
    {
        return Package::orderBy($orderBy, $order)->latest();
    }
    public function getPackage(string $encryptedId): Package|Collection
    {
        return Package::findOrFail(decrypt($encryptedId));
    }
    public function getDeletedPackage(string $encryptedId): Package|Collection
    {
        return Package::onlyTrashed()->findOrFail(decrypt($encryptedId));
    }

    public function createPackage(array $data): Package
    {
        $data['created_by'] = user()->id;
        $normal_title = $data['total_ad'] . " " . Package::tagList()[$data['tag']] . " " . ($data['total_ad'] > 1 ? "ADS" : "AD");
        $agent_title = $data['total_ad'] . " " . ($data['total_ad'] > 1 ? "ADS" : "AD") . " PREM";
        $data['name'] = $data['tag'] == Package::TAG_AGENT_SUBSCR ? $agent_title : $normal_title;
        $package = Package::create($data);
        return $package;
    }

    public function updatePackage(Package $package, array $data): Package
    {
        $data['updated_by'] = user()->id;
        $package->update($data);
        return $package;
    }

    public function delete(Package $package): void
    {
        $package->update(['deleted_by' => user()->id]);
        $package->delete();
    }

    public function restore(string $encryptedId): void
    {
        $package = $this->getDeletedPackage($encryptedId);
        $package->update(['updated_by' => user()->id]);
        $package->restore();
    }

    public function permanentDelete(string $encryptedId): void
    {
        $package = $this->getDeletedPackage($encryptedId);
        $package->forceDelete();
    }

    public function toggleStatus(Package $package): void
    {
        $package->update([
            'status' => !$package->status,
            'updated_by' => user()->id
        ]);
    }
}
