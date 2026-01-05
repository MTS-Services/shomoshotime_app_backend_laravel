<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChapterResource extends JsonResource
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
            'content_id' => $this->content_id,
            'file' => $this->file,
            'file_type' => $this->file_type,
            'content' => $this->whenLoaded('content', function () {
                return new ContentResource($this->content);
            }),


            'created_at' => $this->created_at_formatted ?? $this->created_at,
            'updated_at' => $this->updated_at_formatted ?? $this->updated_at,
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',
        ];
    }
}
