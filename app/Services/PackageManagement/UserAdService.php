<?php

namespace App\Services\PackageManagement;

use App\Models\UserAd;
use Auth;
use Illuminate\Database\Eloquent\Collection;

class UserAdService
{
    public function getUserAds($orderBy = 'sort_order', $order = 'asc')
    {
        return UserAd::orderBy($orderBy, $order)->latest();
    }
    public function getUserAd(string $encryptedId): UserAd|Collection
    {
        return UserAd::findOrFail(decrypt($encryptedId));
    }
    // public function getDeletedPackage(string $encryptedId): Package|Collection
    // {
    //     return Package::onlyTrashed()->findOrFail(decrypt($encryptedId));
    // }

    public function createUserAd(array $data): UserAd
    {
        $data['created_by'] = Auth::id();
        $data['user_id'] = Auth::id();
        $ad = UserAd::updateOrCreate(['order_id' => $data['order_id']], $data);
        return $ad;
    }

    // public function updatePackage(Package $package, array $data): Package
    // {
    //     $data['updated_by'] = user()->id;
    //     $package->update($data);
    //     return $package;
    // }

    // public function delete(Package $package): void
    // {
    //     $package->update(['deleted_by' => user()->id]);
    //     $package->delete();
    // }

    // public function restore(string $encryptedId): void
    // {
    //     $package = $this->getDeletedPackage($encryptedId);
    //     $package->update(['updated_by' => user()->id]);
    //     $package->restore();
    // }

    // public function permanentDelete(string $encryptedId): void
    // {
    //     $package = $this->getDeletedPackage($encryptedId);
    //     $package->forceDelete();
    // }

    // public function toggleStatus(Package $package): void
    // {
    //     $package->update([
    //         'status' => !$package->status,
    //         'updated_by' => user()->id
    //     ]);
    // }
}
