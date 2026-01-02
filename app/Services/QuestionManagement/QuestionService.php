<?php

namespace App\Services\QuestionManagement;

use App\Models\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionService
{
    public function getQuestions(?int $questionId = null, string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        return Question::with('questionSet')->where('question_set_id', $questionId)->orderBy($orderBy, $order)->latest();
    }

  
    public function findData($id): ?Question
    {
        $model = Question::findOrFail($id);
        if (! $model) {
            throw new \Exception('Data not found');
        }

        return $model;
    }

    public function createQuestion(array $data)
    {
        return DB::transaction(function () use ($data) {

            $data['created_by'] = Auth::id();

            return Question::create($data);
        });
    }

    public function updateQuestion( $findData, array $data)
    {
        return DB::transaction(function () use ($findData, $data) {
            $data['updated_by'] = Auth::id();

            $findData->update($data);

            return $findData;
        });
    }

    public function deleteQuestion($findData)
    {
        return DB::transaction(function () use ($findData) {
            $findData->forceDelete();
        });
    }
}
