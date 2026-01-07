<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Support\Facades\Auth;

class MockTestAttempt extends BaseModel
{
     const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'sort_order',
        'user_id',
        'question_set_id',
        'attempt_number',
        'total_questions',
        'questions_answered',
        'correct_answers',
        'wrong_answers',
        'score_percentage',
        'status',
        'started_at',
        'completed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'score_percentage' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
 // Helper Methods
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function complete(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'updated_by' => Auth::id(),
        ]);
    }

    public function getProgress(): float
    {
        if ($this->total_questions === 0) {
            return 0;
        }
        return ($this->questions_answered / $this->total_questions) * 100;
    }

    public function getRemainingQuestions(): int
    {
        return max(0, $this->total_questions - $this->questions_answered);
    }

    public function calculateScorePercentage(): float
    {
        if ($this->total_questions === 0) {
            return 0;
        }
        return ($this->correct_answers / $this->total_questions) * 100;
    }


    
    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

     public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class, 'question_set_id', 'id');
    }


    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
