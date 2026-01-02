<?php

namespace App\Services\QuestionManagement;

use App\Models\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionService
{
   public function getQuestions(?int $questionId = null,string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        return Question::with('questionSet')->where('question_set_id', $questionId)->orderBy($orderBy, $order)->latest();
    }

    public function findData($id)
    {
        return Question::findOrFail($id);
    }
      public function createQuestion(array $data)
    {
        return DB::transaction(function () use ($data) {
            
            $data['created_by'] = Auth::id();

            return Question::create($data);
        });
    }
}
