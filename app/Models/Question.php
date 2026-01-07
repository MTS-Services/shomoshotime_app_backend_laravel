<?php

namespace App\Models;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

     // Helper Methods
    public function getOptions(): array
    {
        return [
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
        ];
    }

    public function isCorrectAnswer(string $answer): bool
    {
        return strtolower(trim($this->answer)) === strtolower(trim($answer));
    }

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class, 'question_set_id', 'id');
    }
     public function answers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class, 'question_id', 'id');
    }
 
}
