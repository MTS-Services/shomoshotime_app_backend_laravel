<?php

namespace App\Http\Resources\API\V1\PropertyManagement;

use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? 'N/A',
            'file-type' => $this->type ?? 'N/A',
            'file-types' => [
                PropertyImage::TYPE_IMAGE . ': ' . 'Image',
                PropertyImage::TYPE_VIDEO . ': ' . 'Video',
                PropertyImage::TYPE_UNKNOWN . ': ' . 'Unknown',
            ],
            'file' => $this->modified_file ?? 'N/A',
            'is_primary' => $this->is_primary ?? 'N/A',
            'is_primary-data' => [
                PropertyImage::PRIMARY . ': ' . 'Yes',
                PropertyImage::NOT_PRIMARY . ': ' . 'No',
            ],
            'created_at' => $this->created_at_formatted,
            'updated_at' => $this->created_at_formatted,
        ];
    }
}
