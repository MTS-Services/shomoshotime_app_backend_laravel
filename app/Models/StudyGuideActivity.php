<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyGuideActivity extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'user_id',
        'content_id',
        'page_number',

        'created_by',
        'updated_by',
    ];

    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id', 'id');
    }

    /**
     * @param  Builder<StudyGuideActivity>  $query
     */
    public function scopeWithinContentPageRange(Builder $query, int $totalPages): Builder
    {
        if ($totalPages <= 0) {
            return $query->whereRaw('0 = 1');
        }

        return $query
            ->where('page_number', '>=', 1)
            ->where('page_number', '<=', $totalPages);
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
