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
            'file' => storage_url($this->file),
            'question' => $this->question,
            'option_a' => $this->option_a,
            'option_b' => $this->option_b,
            'option_c' => $this->option_c,
            'option_d' => $this->option_d,
            'answer' => $this->answer,            
            // User-specific statistics (if loaded)
            // 'user_stats' => $this->when(isset($this->user_answer), [
            //     'answered' => true,
            //     'last_answer' => $this->user_answer->last_answer,
            //     'last_mode' => $this->user_answer->last_mode,
            //     'practice' => [
            //         'total_attempts' => $this->user_answer->getTotalPracticeAttempts(),
            //         'correct_attempts' => $this->user_answer->practice_correct_attempts,
            //         'failed_attempts' => $this->user_answer->practice_failed_attempts,
            //         'accuracy' => round($this->user_answer->getPracticeAccuracy(), 2),
            //         'last_answer' => $this->user_answer->practice_last_answer,
            //         'first_answered_at' => $this->user_answer->practice_first_answered_at?->format('Y-m-d H:i:s'),
            //     ],
            //     'mock_test' => [
            //         'total_attempts' => $this->user_answer->getTotalMockAttempts(),
            //         'correct_attempts' => $this->user_answer->mock_correct_attempts,
            //         'failed_attempts' => $this->user_answer->mock_failed_attempts,
            //         'accuracy' => round($this->user_answer->getMockAccuracy(), 2),
            //         'last_attempt_number' => $this->user_answer->last_mock_attempt_number,
            //     ],
            // ]),
            
            'questionSet' => $this->whenLoaded('questionSet', function () {
                return new QuestionSetResource($this->questionSet);
            }),
            
            'created_at' => $this->created_at_formatted ?? $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at_formatted ?? $this->updated_at->format('Y-m-d H:i:s'),
            'creater_name' => $this->creater?->name ?? 'N/A',
            'updater_name' => $this->updater?->name ?? 'N/A',
        ];
    }
}