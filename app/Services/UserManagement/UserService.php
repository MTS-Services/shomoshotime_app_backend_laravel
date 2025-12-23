<?php

namespace App\Services\UserManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\CompanyInformation; // Added as it's now managed here
use App\Models\User;
use App\Models\UserProfile; // Added as it's now managed here
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Added for password hashing

class UserService
{
    use FileManagementTrait;

    /**
     * Get a query builder for users.
     *
     * @param string $orderBy The column to order by.
     * @param string $order The order direction ('asc' or 'desc').
     * @return Builder
     */
    public function getUsers(string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        return User::orderBy($orderBy, $order)->latest();
    }

    /**
     * Get a user by their encrypted ID.
     *
     * @param string $encryptedId The encrypted ID of the user.
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getUser(string $encryptedId): User
    {
        return User::findOrFail(decrypt($encryptedId));
    }

    /**
     * Get a soft-deleted user by their encrypted ID.
     *
     * @param string $encryptedId The encrypted ID of the user.
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getDeletedUser(string $encryptedId): User
    {
        return User::onlyTrashed()->findOrFail(decrypt($encryptedId));
    }

    /**
     * Get the authenticated user with specified relationships loaded.
     *
     * @param array $relations An array of relationships to load (e.g., ['profile', 'companyInformation']).
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the authenticated user is not found.
     */
    public function getAuthenticatedUserWithRelations(array $relations = []): User
    {
        // Assuming `user()` helper returns the authenticated user instance
        // or using `Auth::user()`. For robustness, checking if user exists.
        $user = Auth::user();

        if (!$user) {
            throw new ModelNotFoundException('Authenticated user not found.');
        }

        return $user->load($relations);
    }

    /**
     * Get the authenticated user's profile.
     *
     * @return UserProfile|null
     */
    public function getAuthenticatedUserProfile(): ?UserProfile
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }
        return UserProfile::firstOrNew(['user_id' => $user->id]);
    }

    /**
     * Get the authenticated user's company information.
     *
     * @return CompanyInformation|null
     */
    public function getAuthenticatedCompanyInformation(): ?CompanyInformation
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }
        if ($user->user_type != User::USER_TYPE_AGENT) {
            return null;
        }
        return CompanyInformation::firstOrNew(['user_id' => $user->id]);
    }

    /**
     * Create a new user.
     *
     * @param array $data The user data.
     * @param mixed $file Optional file for user image.
     * @return User
     * @throws Throwable
     */
    public function createUser(array $data, $file = null): User
    {
        return DB::transaction(function () use ($data, $file) {
            if ($file) {
                $data['image'] = $this->handleFileUpload($file, 'users', $data['name'] ?? 'default_user_image');
            }

            // Hash password before creating the user
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $data['status'] = $data['status'] ?? User::STATUS_ACTIVE;
            $data['created_by'] = Auth::id();

            $user = User::create($data);

            // Optional: Create related profile or company information here if they are mandatory
            // For example: $user->profile()->create([...]);

            return $user;
        });
    }

    /**
     * Update an existing user.
     *
     * @param User $user The user model instance to update.
     * @param array $data The data to update.
     * @param mixed $file Optional new file for user image.
     * @return User
     * @throws Throwable
     */
    public function updateUser(User $user, array $data, $file = null): User
    {
        if ($file) {
            $data['image'] = $this->handleFileUpload($file, 'users', $data['name'] ?? $user->name);
            // Only delete if there was an existing image
            if ($user->image) {
                $this->fileDelete($user->image);
            }
        }
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['updated_by'] = Auth::id();
        $user->update($data);
        return $user;
    }


    /**
     * Soft delete a user.
     *
     * @param User $user The user model instance to soft delete.
     * @return void
     */
    public function delete(User $user): void
    {
        // Ensure 'deleted_by' is set before soft deleting
        $user->update(['deleted_by' => Auth::id()]);
        $user->delete();
    }

    /**
     * Restore a soft-deleted user.
     *
     * @param string $encryptedId The encrypted ID of the user to restore.
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function restore(string $encryptedId): User
    {
        $user = $this->getDeletedUser($encryptedId);
        $user->update(['updated_by' => Auth::id()]);
        $user->restore();
        return $user; // Return the restored user for potential further use
    }

    /**
     * Permanently delete a user.
     *
     * @param string $encryptedId The encrypted ID of the user to permanently delete.
     * @return void
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function permanentDelete(string $encryptedId): void
    {
        $user = $this->getDeletedUser($encryptedId);
        // It's crucial to delete associated files before force deleting the record
        if ($user->image) {
            $this->fileDelete($user->image);
        }
        $user->forceDelete();
    }

    /**
     * Toggle the status of a user.
     *
     * @param User $user The user model instance to toggle status.
     * @return User
     */
    public function toggleStatus(User $user): User
    {
        $user->update([
            'status' => !$user->status,
            'updated_by' => Auth::id()
        ]);
        return $user;
    }

    public function updateCompanyInformation(User $user, array $data, $file = null): CompanyInformation
    {
        if ($file) {
            $data['image'] = $this->handleFileUpload($file, 'users', $data['name'] ?? $user->name);
            if ($user->image) {
                $this->fileDelete($user->image);
            }
        }
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        return CompanyInformation::updateOrCreate(['user_id' => $user->id], $data);
    }
}
