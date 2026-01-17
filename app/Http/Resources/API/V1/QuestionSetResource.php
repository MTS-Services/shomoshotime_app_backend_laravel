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
            'sort_order' => $this->sort_order,
            'category' => $this->category ?? 'N/A',
            'title' => $this->title ?? 'N/A',
            'subtitle' => $this->subtitle ?? 'N/A',
            'status' => $this->status,
            'status_label' => $this->status_label ?? $this->getDifficultyLabel(),
            'status_data' => QuestionSet::getStatusList(),
            'status_color' => $this->status_color ?? $this->getStatusColor(),
            'total_questions' => $this->questions_count ?? $this->getTotalQuestions(),

            // Questions (if loaded)
            'questions' => $this->whenLoaded('questions', function () {
                return QuestionResource::collection($this->questions);
            }),
            'questionAnswers' => $this->whenLoaded('questionAnswers', function () {
                return QuestionAnswerResource::collection($this->questionAnswers);
            }),

            // User Progress Data (if analytics relationship is loaded)
            'analytics' => $this->whenLoaded('analytics', function () {
                return QuestionSetAnalyticResource::collection($this->analytics);
            }),

            // User Progress Data (if analytics relationship is loaded)
            'mockTestAttempts' => $this->whenLoaded('mockTestAttempts', function () {
                return MockTestAttemptResource::collection($this->mockTestAttempts);
            }),

            'created_at' => $this->created_at_formatted ?? $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at_formatted ?? $this->updated_at->format('Y-m-d H:i:s'),
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',
        ];
    }

    /**
     * Get status color based on difficulty
     */
    private function getStatusColor(): string
    {
        return match ($this->status) {
            QuestionSet::STATUS_EASY => 'success',
            QuestionSet::STATUS_MEDIUM => 'warning',
            QuestionSet::STATUS_HARD => 'danger',
            default => 'secondary',
        };
    }
}
