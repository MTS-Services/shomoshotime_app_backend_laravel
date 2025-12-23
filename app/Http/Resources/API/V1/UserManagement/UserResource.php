<?php

namespace App\Http\Resources\API\V1\UserManagement;

use App\Http\Resources\API\V1\PropertyManagement\PropertyResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name ?? 'N/A',
            'email' => $this->email ?? 'N/A',
            'phone' => $this->phone ?? 'N/A',
            'image' => $this->image ?? 'N/A',
            'user_type' => $this->user_type,
            'user_type_label' => $this->user_type_label,
            'user_data' => [
                User::USER_TYPE_INDIVIDUAL . ': ' . 'Individual',
                User::USER_TYPE_AGENT . ': ' . 'Agent'
            ],
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_data' => [
                User::STATUS_ACTIVE . ': ' . 'Active',
                User::STATUS_INACTIVE . ': ' . 'Inactive',
                User::STATUS_PENDING . ': ' . 'Pending',
                User::STATUS_SUSPENDED . ': ' . 'Suspended',
            ],
            'language_preference' => $this->language_preference ?? 1,
            'language_preference_label' => $this->language_preference_label,
            'language_preference_data' => [
                User::LANGUAGE_ARABIC . ': ' . 'ar',
                User::LANGUAGE_ENGLISH . ': ' . 'en',
            ],
            'phone_verified_at' => $this->phone_verified_at,
            'email_verified_at' => $this->email_verified_at,
            'last_login_at' => $this->last_login_at_formatted,
            'is_banned' => $this->is_banned,
            'profile' => $this->whenLoaded('profile') ? new UserProfileResource($this->profile) : null,
            $this->when($this->user_type === User::USER_TYPE_AGENT, [
                'company_information' => $this->whenLoaded('companyInformation') ? new UserCompanyInformationResource($this->companyInformation) : null,
            ]),
            'properties' => $this->whenLoaded('properties', function () {
                return PropertyResource::collection($this->properties);
            }),
            'created_at' => $this->created_at_formatted,
            'updated_at' => $this->updated_at_formatted,
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',
        ];
    }
}
