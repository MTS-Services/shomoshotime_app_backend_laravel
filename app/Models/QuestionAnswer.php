<?php

namespace App\Models;

class QuestionAnswer extends BaseModel
{
     protected $fillable = [
        'sort_order',
        'user_id',
        'question_set_id',
        'question_id',

        'practice_correct_attempts',
        'practice_failed_attempts',
        'practice_last_answer',
        'practice_first_answered_at',

        'mock_correct_attempts',
        'mock_failed_attempts',

        'last_mode',
        'last_answer',
        'last_mock_attempt_number',

        'created_by',
        'updated_by',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'practice_first_answered_at' => 'datetime',
        'last_mock_attempt_number'   => 'integer',
        'practice_correct_attempts' => 'integer',
        'practice_failed_attempts'  => 'integer',
        'mock_correct_attempts'     => 'integer',
        'mock_failed_attempts'      => 'integer',
    ];
    // Helper Methods
    public function getTotalPracticeAttempts(): int
    {
        return $this->practice_correct_attempts + $this->practice_failed_attempts;
    }

    public function getTotalMockAttempts(): int
    {
        return $this->mock_correct_attempts + $this->mock_failed_attempts;
    }

    public function getPracticeAccuracy(): float
    {
        $total = $this->getTotalPracticeAttempts();
        if ($total === 0) {
            return 0;
        }
        return ($this->practice_correct_attempts / $total) * 100;
    }

    public function getMockAccuracy(): float
    {
        $total = $this->getTotalMockAttempts();
        if ($total === 0) {
            return 0;
        }
        return ($this->mock_correct_attempts / $total) * 100;
    }

    public function isFirstTimeAnswering(): bool
    {
        return $this->getTotalPracticeAttempts() === 0 && $this->getTotalMockAttempts() === 0;
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

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
