<?php

namespace App\Models;

use App\Models\BaseModel;

class QuestionSet extends BaseModel
{
     protected $fillable =[
        'sort_order',
        'category',
        'title',
        'subtitle',
        'status',


        'created_by',
        'updated_by',
        'deleted_by',
    ];

    //
    public const STATUS_EASY   = 0;
    public const STATUS_MEDIUM = 1;
    public const STATUS_HARD   = 2;

    /**
     * Status list (same pattern as your example)
     */
    public static function getStatusList(): array
    {
        return [
            self::STATUS_EASY   => 'Easy',
            self::STATUS_MEDIUM => 'Medium',
            self::STATUS_HARD   => 'Hard',
        ];
    }


    
    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */   
    /** Status constants */
 
    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
