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
            'file' => $this->file,
            'file_type' => $this->file_type,
            'type' => $this->type,
            'type_label' => Content::getTypeList()[$this->type] ?? 'N/A',
            'is_publish' => $this->is_publish,
            'is_publish_label' => Content::getPublishList()[$this->is_publish] ?? 'N/A',
            'total_pages' => $this->total_pages,
            'study_guide_activities_count' => $this->when($this->relationLoaded('studyGuideActivities'), $this->studyGuideActivities),

             
            'created_at' => $this->created_at_formatted ?? $this->created_at,
            'updated_at' => $this->updated_at_formatted ?? $this->updated_at,
            
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',
        ];
    }
}
