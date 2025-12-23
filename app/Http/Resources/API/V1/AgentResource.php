<?php

namespace App\Http\Resources\API\V1;

use App\Http\Resources\API\V1\PropertyManagement\PropertyResource;
use App\Http\Resources\API\V1\UserManagement\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentResource extends JsonResource
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
            'whatsapp_number' => $this->whatsapp_number,
            'image' => $this->image,

            'user' => $this->whenLoaded('user') ? new UserResource($this->user) : null,

            'created_at' => $this->created_at_formatted,
            'updated_at' => $this->updated_at_formatted,
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',
        ];
    }
}