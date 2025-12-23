<?php

namespace App\Services\UserManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AdminService
{
    use FileManagementTrait;


    public function getAdmins($orderBy = 'sort_order', $order = 'asc')
    {
        return User::orderBy($orderBy, $order)->latest();
    }
    public function getAdmin(string $encryptedId): User|Collection
    {
        return User::findOrFail(decrypt($encryptedId));
    }
    public function getDeletedAdmin(string $encryptedId): User|Collection
    {
        return User::onlyTrashed()->findOrFail(decrypt($encryptedId));
    }

    public function createAdmin(array $data, $file = null): User
    {
        return DB::transaction(function () use ($data, $file) {
            if ($file) {
                $data['image'] = $this->handleFileUpload($file, 'admins', $data['name']);
            }
            $data['is_admin'] = User::ADMIN;
            $data['status'] = User::STATUS_ACTIVE;
            $data['user_type'] = User::USER_TYPE_ADMIN;
            $data['created_by'] = user()->id;
            $admin = User::create($data);
            return $admin;
        });
    }

    public function updateAdmin(User $admin, array $data, $file = null): User
    {
        return DB::transaction(function () use ($admin, $data, $file) {
            if ($file) {
                $data['image'] = $this->handleFileUpload($file, 'admins', $data['name']);
                $this->fileDelete($admin->image);
            }
            $data['password'] = $data['password'] ?? $admin->password;
            $data['updated_by'] = user()->id;
            $admin->update($data);
            return $admin;
        });
    }

    public function delete(User $admin): void
    {
        $admin->update(['deleted_by' => user()->id]);
        $admin->delete();
    }

    public function restore(string $encryptedId): void
    {
        $admin = $this->getDeletedAdmin($encryptedId);
        $admin->update(['updated_by' => user()->id]);
        $admin->restore();
    }

    public function permanentDelete(string $encryptedId): void
    {
        $admin = $this->getDeletedAdmin($encryptedId);
        $admin->forceDelete();
    }

    public function toggleStatus(User $admin): void
    {
        $admin->update([
            'status' => !$admin->status,
            'updated_by' => user()->id
        ]);
    }
}
