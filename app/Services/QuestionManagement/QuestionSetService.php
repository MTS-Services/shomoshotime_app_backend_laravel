<?php

namespace App\Services\QuestionManagement;

use App\Models\QuestionSet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionSetService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getQuestionSets(string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        return QuestionSet::orderBy($orderBy, $order)->latest();
    }

    public function findData($id): ?QuestionSet
    {
        $model = QuestionSet::findOrFail($id);
        if (! $model) {
            throw new \Exception('Data not found');
        }

        return $model;
    }

    public function createQuestion(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['status'] = $data['status'] ?? QuestionSet::STATUS_EASY;
            $data['created_by'] = Auth::id();

            return QuestionSet::create($data);
        });
    }

  public function updateQuestion($findData, array $data)
{
    return DB::transaction(function () use ($findData, $data) {
        $data['updated_by'] = Auth::id();

        $findData->update($data);

        return $findData; 
    });
}

}
