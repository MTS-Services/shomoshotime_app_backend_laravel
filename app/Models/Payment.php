<?php

namespace App\Models;

use App\Models\BaseModel;

class Payment extends BaseModel
{
    protected $fillable = [
        "sort_order",
        "user_id",
        "subscription_id",
        "amount",
        "currency",
        "payment_method",
        "transaction_id",
        "status",


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
