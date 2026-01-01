<?php

namespace App\Http\Resources\API\V1;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
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
            'sort_order' => $this->sort_order,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'category' => $this->category,
            'type' => $this->type,
            'type_label' => Content::getTypeList()[$this->type] ?? 'N/A',
            'filetype' => $this->filetype,
            'file' => $this->file,

            'created_at' => $this->created_at_formatted ?? $this->created_at,
            'updated_at' => $this->updated_at_formatted ?? $this->updated_at,
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',
        ];
    }
}
