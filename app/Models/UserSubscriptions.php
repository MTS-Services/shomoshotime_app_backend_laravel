<?php

namespace App\Models;

class UserSubscriptions extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'user_id',
        'payment_id',
        'subscription_id',

        'starts_at',
        'ends_at',
        'is_active',

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
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }
    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
