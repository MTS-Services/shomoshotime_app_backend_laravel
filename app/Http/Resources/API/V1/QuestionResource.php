<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'question_set_id' => $this->question_set_id,
            'file' => $this->file,
            'question' => $this->question,
            'option_a' => $this->option_a,
            'option_b' => $this->option_b,
            'option_c' => $this->option_c,
            'option_d' => $this->option_d,
            'answer' => $this->answer,
            'questionSet' => $this->whenLoaded('questionSet', function () {
                return new QuestionSetResource($this->questionSet);
            }),
            'created_at' => $this->created_at_formatted ?? $this->created_at,
            'updated_at' => $this->updated_at_formatted ?? $this->updated_at,
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',
        ];
    }
}
