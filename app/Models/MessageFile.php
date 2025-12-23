<?php

namespace App\Models;

use App\Models\BaseModel;

class MessageFile extends BaseModel
{
     protected $fillable =[

        'message_id',
        'file',
        'type', 
        
        'created_by',
        'updated_by',
        'deleted_by',
    ];

   public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge(parent::getAppends(), [
            'modified_file',

        ]);
    }

    public const TYPE_TEXT = 1;
    public const TYPE_IMAGE = 2;
    public const TYPE_VIDEO = 3;
    public const TYPE_AUDIO = 4;
    public const TYPE_FILE = 5;

    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    // Relations
    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id','id');
    }

    
    public function getModifiedFileAttribute(): string
    {
        return $this->file ? asset('storage/' . $this->file) : null;
    }
    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
