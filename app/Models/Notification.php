<?php

namespace App\Models;

use App\Models\BaseModel;

class Notification extends BaseModel
{
     protected $fillable =[
        'sort_order',
        'user_id',
        'title',
        'message',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    //


    
    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    // Relations

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
