<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        if ($this->mock_test_attempts >= 3) {
            return false;
        }

        $questionSet = $this->relationLoaded('questionSet')
            ? $this->questionSet
            : $this->questionSet()->first();

        if (! $questionSet || (int) $questionSet->type !== QuestionSet::TYPE_MOCK_TEST) {
            return false;
        }

        if ($this->hasActiveMockTestAttempt()) {
            return false;
        }

        return true;
    }

    public function hasCompletedAllMockTests(): bool
    {
        return $this->mock_test_attempts >= 3;
    }

    public function hasActiveMockTestAttempt(): bool
    {
        return MockTestAttempt::query()
            ->where('user_id', $this->user_id)
            ->where('question_set_id', $this->question_set_id)
            ->where('status', MockTestAttempt::STATUS_IN_PROGRESS)
            ->exists();
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function questionSet(): BelongsTo
    {
        return $this->belongsTo(QuestionSet::class, 'question_set_id', 'id');
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
