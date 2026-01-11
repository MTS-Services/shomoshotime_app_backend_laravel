<?php

namespace App\Http\Resources\API\V1;

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
            'image' => storage_url($this->image) ?? 'N/A',
            'status' => $this->status,
            'is_admin' => $this->is_admin,
            'status_label' => $this->status_label,
            'status_data' => User::getStatusList(),
            'email_verified_at' => $this->email_verified_at,
            'last_login_at' => $this->last_login_at_formatted,
            'created_at' => $this->created_at_formatted,
            'updated_at' => $this->updated_at_formatted,
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',

        ];
    }
}
