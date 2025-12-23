<?php

namespace App\Services\PackageManagement;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    public function getOrders($orderBy = 'sort_order', $order = 'asc')
    {
        return Order::orderBy($orderBy, $order)->latest();
    }
    public function getOrder(string $encryptedId): Order|Collection
    {
        return Order::findOrFail(decrypt($encryptedId));
    }
    // public function getDeletedPackage(string $encryptedId): Package|Collection
    // {
    //     return Package::onlyTrashed()->findOrFail(decrypt($encryptedId));
    // }

    public function createOrder(array $data): Order
    {
        $data['created_by'] = Auth::id();
        $data['order_id'] = generateOrderID();
        $data['user_id'] = Auth::id();
        $order = Order::create($data);
        return $order;
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
