<?php

namespace App\Http\Resources\API\V1;

use App\Models\QuestionSet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionSetResource extends JsonResource
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
            'category' => $this->category ?? 'N/A',
            'title' => $this->title ?? 'N/A',
            'subtitle' => $this->subtitle ?? 'N/A',
            'status' => $this->status,
            'status_data' => QuestionSet::getStatusList(),
            'created_at' => $this->created_at_formatted,
            'updated_at' => $this->updated_at_formatted,
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',

        ];
    }
}
