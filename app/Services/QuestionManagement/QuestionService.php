<?php

namespace App\Services\QuestionManagement;

use App\Models\Question;
use Illuminate\Database\Eloquent\Builder;

class QuestionService
{
   public function getQuestions(?int $questionId = null,string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        return Question::with('questionSet')->where('question_set_id', $questionId)->orderBy($orderBy, $order)->latest();
    }

}
