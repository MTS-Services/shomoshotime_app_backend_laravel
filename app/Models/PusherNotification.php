<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PusherNotification extends BaseModel
{
     protected $fillable =[
        'sort_order',
        'user_id',
        'title',
        'message',
        'is_read',


        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];
    
    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
