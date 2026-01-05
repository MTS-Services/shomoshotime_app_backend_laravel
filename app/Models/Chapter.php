<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chapter extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'content_id',
        'file',
        'file_type',

        'created_by',
        'updated_by',
    ];

    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id', 'id');
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
