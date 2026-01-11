<?php

namespace App\Models;

use App\Models\BaseModel;
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

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
