<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MockTestAttemptResource extends JsonResource
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
            'attempt_number' => $this->attempt_number,
            'total_questions' => $this->total_questions,
            'questions_answered' => $this->questions_answered,
            'remaining_questions' => $this->getRemainingQuestions(),
            'correct_answers' => $this->correct_answers,
            'wrong_answers' => $this->wrong_answers,
            'score_percentage' => (float) $this->score_percentage,
            'progress_percentage' => round($this->getProgress(), 2),
            'status' => $this->status,
            'is_completed' => $this->isCompleted(),
            'is_in_progress' => $this->isInProgress(),
            'grade' => $this->getGrade(),
            'started_at' => $this->started_at->format('Y-m-d H:i:s'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s'),
            'duration_minutes' => $this->completed_at 
                ? $this->started_at->diffInMinutes($this->completed_at)
                : null,
            'duration_formatted' => $this->completed_at 
                ? $this->started_at->diffForHumans($this->completed_at, true)
                : null,
            
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
        ];
    }

    /**
     * Get grade based on score percentage
     */
    private function getGrade(): string
    {
        $percentage = (float) $this->score_percentage;
        
        return match(true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default => 'F',
        };
    }
}