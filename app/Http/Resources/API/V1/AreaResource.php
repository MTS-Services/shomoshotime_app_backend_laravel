<?php

namespace App\Http\Resources\API\V1;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
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
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_data' => [
                Area::STATUS_ACTIVE . ': ' . 'Active',
                Area::STATUS_INACTIVE . ': ' . 'Inactive',
            ],
            'created_at' => $this->created_at_formatted,
            'properties_count' => $this->properties_count ?? '0',
        ];
    }
}
