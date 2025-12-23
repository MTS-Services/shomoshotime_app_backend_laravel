<?php

namespace App\Models;



class CompanyInformation extends BaseModel
{
     protected $fillable=[
        'sort_order',
        'user_id',
        'company_name',
        'company_description',
        'address',
        'social_links',
        'website',
     ];


    
    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    // Relations
     public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
