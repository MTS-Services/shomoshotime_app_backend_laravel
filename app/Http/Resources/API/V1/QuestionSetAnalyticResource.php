<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionSetAnalyticResource extends JsonResource
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
            'question_set_id' => $this->question_set_id,
            'current_mode' => $this->current_mode,
            'is_practice_mode' => $this->isPracticeMode(),
            'is_mock_test_mode' => $this->isMockTestMode(),
            
            'practice' => [
                'questions_answered' => $this->practice_questions_answered,
                'correct_answers' => $this->practice_correct_answers,
                'completed' => $this->practice_completed,
                'progress_percentage' => round($this->getPracticeProgress(), 2),
                'accuracy' => $this->practice_questions_answered > 0
                    ? round(($this->practice_correct_answers / $this->practice_questions_answered) * 100, 2)
                    : 0,
                'completed_at' => $this->practice_completed_at?->format('Y-m-d H:i:s'),
            ],
            
            'mock_test' => [
                'total_attempts' => $this->mock_test_attempts,
                'remaining_attempts' => $this->getRemainingMockAttempts(),
                'can_start' => $this->canStartMockTest(),
                'has_completed_all' => $this->hasCompletedAllMockTests(),
                'best_score' => $this->best_mock_score,
                'best_percentage' => (float) $this->best_mock_percentage,
                'current_attempt_number' => $this->current_mock_attempt_number,
                'current_questions_answered' => $this->current_mock_questions_answered,
                'is_in_progress' => $this->isMockTestMode(),
            ],
            
            'question_set' => $this->whenLoaded('questionSet', function () {
                return new QuestionSetResource($this->questionSet);
            }),
            
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}