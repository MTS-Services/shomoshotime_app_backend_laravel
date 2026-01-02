<?php

namespace App\Services\QuestionManagement;

use App\Models\QuestionSet;
use Illuminate\Database\Eloquent\Builder;

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
}
