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
            'file' => storage_url($this->file),
            'file_type' => $this->file_type,
            'file_url' => $this->file_url,
            'type' => $this->type,
            'type_label' => Content::getTypeList()[$this->type] ?? 'N/A',
            'is_publish' => $this->is_publish,
            'is_publish_label' => Content::getPublishList()[$this->is_publish] ?? 'N/A',
            'total_pages' => $this->total_pages,
            'study_guide_activities_count' => $this->study_guide_activities_count ?? 0,
            'study_guide_percent_completed' => $this->studyGuidePercentCompleted(),

            'flash_card_activities_count' => $this->flash_card_activities_count ?? 0,
            'flash_cards_count' => $this->flash_cards_count ?? 0,
            'flash_card_percent_completed' => $this->flashCardPercentCompleted(),

            'created_at' => $this->created_at_formatted ?? $this->created_at,
            'updated_at' => $this->updated_at_formatted ?? $this->updated_at,

            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',
        ];
    }

    private function studyGuidePercentCompleted(): float
    {
        $totalPages = (int) $this->total_pages;
        if ($totalPages <= 0) {
            return 0;
        }

        $attemptedPages = (int) ($this->study_guide_activities_count ?? 0);

        return min(100, round(($attemptedPages / $totalPages) * 100, 2));
    }

    private function flashCardPercentCompleted(): float
    {
        $totalCards = (int) ($this->flash_cards_count ?? 0);
        if ($totalCards <= 0) {
            return 0;
        }

        $attemptedCards = (int) ($this->flash_card_activities_count ?? 0);

        return min(100, round(($attemptedCards / $totalCards) * 100, 2));
    }
}
