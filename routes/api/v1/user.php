<?php

use App\Http\Controllers\API\V1\NotificationController;
use App\Http\Controllers\API\V1\UserPanel\ContentController;
use App\Http\Controllers\API\V1\UserPanel\ProfileController;
use App\Http\Controllers\API\V1\UserPanel\QuestionController;
use App\Http\Controllers\API\V1\UserPanel\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::controller(ContentController::class)->prefix('content')->group(function () {
    Route::post('/study-guides', 'studyGuides')->name('study-guides');
    Route::post('/flash-cards', 'flashCards')->name('flash-cards');
    Route::post('/flash-cards/sets', 'flashCardSets')->name('flash-cards.sets');
    Route::post('/next-page', 'nextPage')->name('next-page');
    Route::post('/flash-cards/next-question', 'nextQuestion')->name('flash-cards.next-question');
});
// Route::controller(QuestionController::class)->prefix('question')->group(function () {
//     Route::post('/sets', 'getQuestionSets')->name('question-sets');
// });

Route::controller(NotificationController::class)->group(function () {
    Route::post('/send-notification', 'sendNotification')->name('send-notification');
    Route::post('/user-notifications', 'getNotifications')->name('get-notifications');
    Route::post('/mark-notification-read', 'markAsRead')->name('mark-notification-read');
});

Route::controller(QuestionController::class)->prefix('question')->group(function () {    
    // Question Sets
    Route::post('/sets', 'getQuestionSets')->name('question-sets');    
    // Questions
    Route::post('/sets/questions', 'getQuestions')->name('get-questions');    
    // Answer Submission
    Route::post('/submit-answer', 'submitAnswer')->name('submit-answer');    
    // Mock Test
    Route::post('/start-mock-test', 'startMockTest')->name('start-mock-test');
    Route::post('/mock-tests/result', 'getMockTestResult')->name('mock-test-result');
    Route::post('/mock-tests/all-results', 'getAllMockTestResults')->name('all-mock-test-results');    
    // Progress & Statistics
    Route::post('/sets/progress', 'getProgress')->name('get-progress');
    Route::post('/sets/questions/statistics', 'getQuestionStatistics')->name('question-statistics');
    Route::post('/sets/analytics', 'getAnalytics')->name('question-set-analytics');    
    // Dashboard/Summary
    Route::post('/dashboard', 'getDashboard')->name('question-dashboard');
});

Route::controller(ProfileController::class)->prefix('profile')->group(function () {
    Route::post('/', 'getProfile')->name('profile');
    Route::post('/update', 'updateProfile')->name('update.profile');
});

Route::controller(SubscriptionController::class)->prefix('subscription')->group(function () {
    Route::post('/list', 'getSubscriptions')->name('subscription.list');
});
