<?php

namespace App\Http\Resources\API\V1\PackageManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'name' => $this->name ?? 'N/A',
            'tag' => $this->tag ?? 'N/A',
            'tag_label' => $this->tag_label ?? 'N/A',
            'tag_list' => $this->tag_list ?? 'N/A',
            'status' => $this->status ?? 'N/A',
            'status_label' => $this->status_label ?? 'N/A',
            'status_list' => $this->status_list ?? 'N/A',
            'price' => $this->price ?? 'N/A',
            'currency' => "KWD",
            'total_ad' => $this->total_ad ?? 'N/A',
            'created_at' => $this->created_at_formatted,
        ];
    }
}
