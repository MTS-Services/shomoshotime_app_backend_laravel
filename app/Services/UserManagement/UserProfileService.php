<?php

namespace App\Services\UserManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;

class UserProfileService
{
    use FileManagementTrait;

    public function updateUserProfile(array $data, $userId = null): UserProfile
    {
        if (!$userId) {
            $userId = Auth::id();
        }
        $profile = UserProfile::firstOrNew(['user_id' => $userId]);
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $profile->update($data);
        $profile->save();
        return $profile;
    }
}
