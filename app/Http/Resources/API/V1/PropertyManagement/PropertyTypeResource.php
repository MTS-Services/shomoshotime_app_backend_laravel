<?php

namespace App\Http\Resources\API\V1\PropertyManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id ?? 'N/A',
            'name'         => $this->name ?? 'N/A',
            'slug'         => $this->slug ?? 'N/A',
            'created_at' => $this->created_at_formatted,
        ];
    }
}
