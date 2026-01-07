<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionSet extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'category',
        'title',
        'subtitle',
        'status',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    //
    public const STATUS_EASY = 0;

    public const STATUS_MEDIUM = 1;

    public const STATUS_HARD = 2;

    /**
     * Status list (same pattern as your example)
     */
    public static function getStatusList(): array
    {
        return [
            self::STATUS_EASY => 'Easy',
            self::STATUS_MEDIUM => 'Medium',
            self::STATUS_HARD => 'Hard',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusList()[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_EASY => 'badge-success',
            self::STATUS_MEDIUM => 'badge-error',
            self::STATUS_HARD => 'badge-warning',
            default => 'badge-default',
        };
    }

    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'question_set_id', 'id');
    }
      public function analytics(): HasMany
    {
        return $this->hasMany(QuestionSetAnalytic::class);
    }

    public function mockTestAttempts(): HasMany
    {
        return $this->hasMany(MockTestAttempt::class);
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */

  
    // Helper Methods
    public function getTotalQuestions(): int
    {
        return $this->questions()->count();
    }

    public function getDifficultyLabel(): string
    {
        return match($this->status) {
            self::STATUS_EASY => 'Easy',
            self::STATUS_MEDIUM => 'Medium',
            self::STATUS_HARD => 'Hard',
            default => 'Unknown',
        };
    }

    // Scope for filtering by difficulty
    public function scopeEasy($query)
    {
        return $query->where('status', self::STATUS_EASY);
    }

    public function scopeMedium($query)
    {
        return $query->where('status', self::STATUS_MEDIUM);
    }

    public function scopeHard($query)
    {
        return $query->where('status', self::STATUS_HARD);
    }
}
