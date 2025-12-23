<?php

namespace App\Models;

use App\Models\BaseModel;

class MessageRead extends BaseModel
{
     protected $fillable =[

        'sort_order',
        'message_id',
        'user_id',
        'read_at',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

   
    
    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */
    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
