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

    // /**
    //  * Create a new user.
    //  *
    //  * @param array $data The user data.
    //  * @param mixed $file Optional file for user image.
    //  * @return User
    //  * @throws Throwable
    //  */
    // public function createUser(array $data, $file = null): User
    // {
    //     return DB::transaction(function () use ($data, $file) {
    //         if ($file) {
    //             $data['image'] = $this->handleFileUpload($file, 'users', $data['name'] ?? 'default_user_image');
    //         }
    //         if (isset($data['password'])) {
    //             $data['password'] = Hash::make($data['password']);
    //         }
    //         $data['status'] = $data['status'] ?? User::STATUS_ACTIVE;
    //         $data['created_by'] = Auth::id();

    //         $user = User::create($data);
    //         return $user;
    //     });
    // }
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

    // /**
    //  * Update an existing user.
    //  *
    //  * @param User $user The user model instance to update.
    //  * @param array $data The data to update.
    //  * @param mixed $file Optional new file for user image.
    //  * @return User
    //  * @throws Throwable
    //  */
    // public function updateUser(User $user, array $data, $file = null): User
    // {
    //     if ($file) {
    //         $data['image'] = $this->handleFileUpload($file, 'users', $data['name'] ?? $user->name);
    //         // Only delete if there was an existing image
    //         if ($user->image) {
    //             $this->fileDelete($user->image);
    //         }
    //     }
    //     if (isset($data['password']) && !empty($data['password'])) {
    //         $data['password'] = Hash::make($data['password']);
    //     } else {
    //         unset($data['password']);
    //     }

    //     $data['updated_by'] = Auth::id();
    //     $user->update($data);
    //     return $user;
    // }

    // /**
    //  * Soft delete a user.
    //  *
    //  * @param User $user The user model instance to soft delete.
    //  * @return void
    //  */
    // public function delete(User $user): void
    // {
    //     $user->delete();
    // }

    // /**
    //  * Toggle the status of a user.
    //  *
    //  * @param User $user The user model instance to toggle status.
    //  * @return User
    //  */
    // public function toggleStatus(User $user): User
    // {
    //     $user->update([
    //         'status' => !$user->status,
    //         'updated_by' => Auth::id()
    //     ]);
    //     return $user;
    // }
}
