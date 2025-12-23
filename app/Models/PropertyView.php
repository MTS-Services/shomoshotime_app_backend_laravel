<?php

namespace App\Models;

use App\Models\BaseModel;

class PropertyView extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'property_id',
        'user_id',
        'ip_address',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    //



    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
