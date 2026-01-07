<?php

namespace App\Models;

use App\Models\BaseModel;

class QuestionSetAnalytic extends BaseModel
{
   protected $fillable = [
        'sort_order',
        'user_id',
        'question_set_id',
        'practice_questions_answered',
        'practice_correct_answers',
        'practice_completed',
        'practice_completed_at',
        'mock_test_attempts',
        'current_mock_questions_answered',
        'best_mock_score',
        'best_mock_percentage',
        'current_mode',
        'current_mock_attempt_number',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
         'practice_completed' => 'boolean',
        'practice_completed_at' => 'datetime',
        'best_mock_percentage' => 'decimal:2',
    ];

      public function canStartMockTest(): bool
    {
        return $this->practice_completed && $this->mock_test_attempts < 3;
    }

    public function hasCompletedAllMockTests(): bool
    {
        return $this->mock_test_attempts >= 3;
    }

    public function isPracticeMode(): bool
    {
        return $this->current_mode === 'practice';
    }

    public function isMockTestMode(): bool
    {
        return $this->current_mode === 'mock_test';
    }

    public function getRemainingMockAttempts(): int
    {
        return max(0, 3 - $this->mock_test_attempts);
    }

    public function getPracticeProgress(): float
    {
        $totalQuestions = $this->questionSet->questions()->count();
        if ($totalQuestions === 0) {
            return 0;
        }
        return ($this->practice_questions_answered / $totalQuestions) * 100;
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
