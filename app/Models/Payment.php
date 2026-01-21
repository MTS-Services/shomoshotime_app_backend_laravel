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
        "payment_intent_data",
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id', 'id');
    }
    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscriptions::class, 'payment_id', 'id');
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
