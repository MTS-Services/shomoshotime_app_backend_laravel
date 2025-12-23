<?php

namespace App\Http\Resources\API\V1\UserManagement;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'dob' => $this->dob ?? 'N/A',
            'gender' => $this->gender,
            'gender_label' => $this->gender_label,
            'gender_data' => [
                UserProfile::GENDER_MALE . ': ' . 'Male',
                UserProfile::GENDER_FEMALE . ': ' . 'Female',
                UserProfile::GENDER_OTHER . ': ' . 'Other',
            ],
            'city' => $this->city ?? 'N/A',
            'country' => $this->country ?? 'N/A',
            'postal_code' => $this->postal_code ?? 'N/A',
            'bio' => $this->bio ?? 'N/A',
            'website' => $this->website ?? 'N/A',
            'social_links' => $this->social_links ?? 'N/A',

            'created_at' => $this->created_at_formatted,
            'updated_at' => $this->updated_at_formatted,
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',
        ];
    }
}
