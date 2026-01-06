<?php

namespace App\Models;
use App\Models\BaseModel;
class Question extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'question_set_id',
        'file',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'answer',
        'created_by',
        'updated_by',
    ];

   

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class, 'question_set_id', 'id');
    }

 
}
