<?php

namespace App\Http\Resources\API\V1\UserManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCompanyInformationResource extends JsonResource
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
            'company_name' => $this->company_name ?? 'N/A',
            'company_description' => $this->company_description ?? 'N/A',
            'address' => $this->address ?? 'N/A',
            'social_links' => $this->social_links ?? 'N/A',
            'website' => $this->website ?? 'N/A',

            'created_at' => $this->created_at_formatted,
            'updated_at' => $this->updated_at_formatted,
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',
        ];
    }
}
