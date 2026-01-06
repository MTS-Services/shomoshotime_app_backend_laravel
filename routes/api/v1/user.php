<?php


use App\Http\Controllers\API\V1\UserPanel\ContentController;
use App\Http\Controllers\API\V1\UserPanel\QuestionController;
use Illuminate\Support\Facades\Route;

Route::controller(ContentController::class)->prefix('content')->group(function () {
    Route::post('/study-guides', 'studyGuides')->name('study-guides');
    Route::post('/flash-cards', 'flashCards')->name('flash-cards');
    Route::post('/flash-cards/sets', 'flashCardSets')->name('flash-cards.sets');
});
Route::controller(QuestionController::class)->prefix('question')->group(function () {
    Route::post('/sets', 'getQuestionSets')->name('question-sets');
});