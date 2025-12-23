<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'conversation_id',
        'user_id',
        'last_read_message_id',
        'joined_at',
        'is_muted',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    //



    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    // Relations
   

    public function conversation() : BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function message() : BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_read_message_id', 'id');
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}


