<?php

namespace App\Models;

use App\Models\BaseModel;

class Favorite extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'user_id',
        'property_id',

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
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
