<?php

namespace App\Models;

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
    /** Status constants */

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
