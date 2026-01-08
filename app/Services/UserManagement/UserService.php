<?php

namespace App\Services\UserManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Added for password hashing

class UserService
{
    use FileManagementTrait;

    /**
     * Get a query builder for users.
     *
     * @param  string  $orderBy  The column to order by.
     * @param  string  $order  The order direction ('asc' or 'desc').
     */
    public function getUsers(string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        return User::orderBy($orderBy, $order)->latest();
    }

    /**
     * Get a user by their encrypted ID.
     *
     * @param  string  $encryptedId  The encrypted ID of the user.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getUser($id): User
    {
        return User::findOrFail($id);
    }

    public function statusChange(User $user, $status)
    {
        $user->status = $status;
        $user->updated_by = request()->user()->id;
        $user->update();
    }
    
    public function createUser(array $data, $image = null): User
    {
        return DB::transaction(function () use ($data, $image) {

            if ($image) {
                $data['image'] = $this->handleFileUpload(
                    $image,
                    'users',
                    $data['name'] ?? 'user'
                );
            }

            $data['password'] = Hash::make($data['password']);
            $data['status'] = $data['status'] ?? User::STATUS_ACTIVE;
            $data['created_by'] = Auth::id();

            return User::create($data);
        });
    }

    public function updateUser(User $user, array $data, $image = null): User
    {
        return DB::transaction(function () use ($user, $data, $image) {

            // Handle image upload if provided
            if ($image) {
                $data['image'] = $this->handleFileUpload(
                    $image,
                    'users',
                    $data['name'] ?? $user->name
                );

                // Delete old image if it exists
                if ($user->image) {
                    $this->fileDelete($user->image);
                }
            }

            // Hash password if provided, otherwise remove from data
            if (isset($data['password']) && ! empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // Track who updated the user
            $data['updated_by'] = Auth::id();

            // Update the user
            $user->update($data);

            return $user;
        });
    }
    public function deleteUser(User $user): void
    {
        
        $user->delete();

        if ($user->image) {
            $this->fileDelete($user->image);
        }
    }

    
}
