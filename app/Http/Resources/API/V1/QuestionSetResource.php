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
            
            // User Progress Data (if analytics relationship is loaded)
            'user_progress' => $this->when(
                $this->relationLoaded('analytics') && $this->analytics->isNotEmpty(),
                function () {
                    $analytic = $this->analytics->first();
                    return [
                        'current_mode' => $analytic->current_mode,
                        'is_practice_mode' => $analytic->isPracticeMode(),
                        'is_mock_test_mode' => $analytic->isMockTestMode(),
                        
                        'practice' => [
                            'completed' => $analytic->practice_completed,
                            'questions_answered' => $analytic->practice_questions_answered,
                            'correct_answers' => $analytic->practice_correct_answers,
                            'progress_percentage' => round($analytic->getPracticeProgress(), 2),
                            'completed_at' => $analytic->practice_completed_at?->format('Y-m-d H:i:s'),
                        ],
                        
                        'mock_test' => [
                            'attempts_used' => $analytic->mock_test_attempts,
                            'attempts_remaining' => $analytic->getRemainingMockAttempts(),
                            'can_start' => $analytic->canStartMockTest(),
                            'has_completed_all' => $analytic->hasCompletedAllMockTests(),
                            'best_score' => $analytic->best_mock_score,
                            'best_percentage' => (float) $analytic->best_mock_percentage,
                            'current_attempt' => $analytic->isMockTestMode() ? [
                                'attempt_number' => $analytic->current_mock_attempt_number,
                                'questions_answered' => $analytic->current_mock_questions_answered,
                                'progress_percentage' => $this->getTotalQuestions() > 0 
                                    ? round(($analytic->current_mock_questions_answered / $this->getTotalQuestions()) * 100, 2)
                                    : 0,
                            ] : null,
                        ],
                    ];
                }
            ),
            
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
        return match($this->status) {
            QuestionSet::STATUS_EASY => 'success',
            QuestionSet::STATUS_MEDIUM => 'warning',
            QuestionSet::STATUS_HARD => 'danger',
            default => 'secondary',
        };
    }
}