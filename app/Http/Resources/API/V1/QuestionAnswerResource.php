<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionAnswerResource extends JsonResource
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
            'question_id' => $this->question_id,
            'question_set_id' => $this->question_set_id,
            'user_id' => $this->user_id,
            
            'practice' => [
                'correct_attempts' => $this->practice_correct_attempts,
                'failed_attempts' => $this->practice_failed_attempts,
                'total_attempts' => $this->getTotalPracticeAttempts(),
                'accuracy' => round($this->getPracticeAccuracy(), 2),
                'last_answer' => $this->practice_last_answer,
                'first_answered_at' => $this->practice_first_answered_at?->format('Y-m-d H:i:s'),
            ],
            
            'mock_test' => [
                'correct_attempts' => $this->mock_correct_attempts,
                'failed_attempts' => $this->mock_failed_attempts,
                'total_attempts' => $this->getTotalMockAttempts(),
                'accuracy' => round($this->getMockAccuracy(), 2),
                'last_attempt_number' => $this->last_mock_attempt_number,
            ],
            
            'last_mode' => $this->last_mode,
            'last_answer' => $this->last_answer,
            'is_first_time' => $this->isFirstTimeAnswering(),
            
            'question' => $this->whenLoaded('question', function () {
                return new QuestionResource($this->question);
            }),
            
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}