<?php

namespace App\Models;

use App\Models\BaseModel;

class UserSubscriptions extends BaseModel
{
    protected $fillable = [
        "sort_order",
        "user_id",
        "subscription_id",
        "starts_at",
        "ends_at",
        "is_active",


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
