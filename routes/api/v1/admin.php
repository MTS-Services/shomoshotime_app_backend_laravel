<?php

use App\Http\Controllers\API\V1\CmsPageController;
use App\Http\Controllers\API\V1\ContentManagement\ContentController;
use App\Http\Controllers\API\V1\ContentManagement\FlashCardController;
use App\Http\Controllers\API\V1\NotificationController;
use App\Http\Controllers\API\V1\QuestionManagement\QuestionController;
use App\Http\Controllers\API\V1\QuestionManagement\QuestionSetController;
use App\Http\Controllers\API\V1\SubscriptionController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\UserPanel\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->prefix('user')->group(function () {
    Route::post('/all', 'getUsers')->name('all-users');
    Route::post('/single/{id}', 'getUser')->name('single-user');
    Route::post('/status-change', 'statusChange')->name('status-change');
    Route::post('/create', 'store')->name('create-user');
    Route::put('/update/{id}', 'update')->name('user.update');
    Route::delete('/delete/{id}', 'delete')->name('user.delete');
});
Route::controller(AnalyticsController::class)->group(function () {
    Route::post('/adminAnalytics', 'adminAnalytics')->name('admin-analytics');
});
Route::controller(CmsPageController::class)->prefix('cms-pages')->group(function () {
    Route::post('/list', 'index')->name('cms-pages.index');
    Route::post('/save', 'storeAndUpdate')->name('cms-pages.save');
});
Route::prefix('content')->group(function () {
    // Content routes
    Route::controller(ContentController::class)->group(function () {
        Route::post('/list', 'getContents')->name('content.list');
        Route::post('/create', 'store')->name('content.create');
        Route::post('/update/{id}', 'update')->name('content.update');
        Route::delete('/delete/{id}', 'destroy')->name('content.delete');
    });

  
    // Flash Card routes
    Route::controller(FlashCardController::class)->prefix('flash-card')->group(function () {
        Route::post('/list', 'getFlashCards')->name('flash-card.list');
        Route::post('/create', 'create')->name('flash-card.create');
        Route::put('/update/{id}', 'update')->name('flash-card.update');
        Route::delete('/delete/{id}', 'delete')->name('flash-card.delete');
    });

});
Route::prefix('question')->group(function () {
    Route::controller(QuestionController::class)->group(function () {
        Route::post('/list', 'getQuestions')->name('question.list');
        Route::post('/create', 'store')->name('question.create');
        Route::put('/update/{id}', 'update')->name('question.update');
        Route::delete('/delete/{id}', 'delete')->name('question.delete');
    });
    Route::controller(QuestionSetController::class)->prefix('set')->group(function () {
        Route::post('/list', 'getQuestionSets')->name('question-set.list');
        Route::post('/create', 'store')->name('question-set.create');
        Route::put('/update/{id}', 'update')->name('question-set.update');
        Route::delete('/delete/{id}', 'delete')->name('question-set.delete');
    });
});

Route::controller(NotificationController::class)->group(function () {
    Route::post('/send-notification', 'sendNotification')->name('send-notification');
    Route::post('/all-notification', 'getAllNotifications')->name('all-notification');
});

Route::controller(SubscriptionController::class)->prefix('subscription')->group(function () {
    Route::post('/list', 'getSubscriptions')->name('subscription.list');
    Route::post('/create', 'store')->name('subscription.create');
    Route::put('/update/{id}', 'update')->name('subscription.update');
    Route::delete('/delete/{id}', 'delete')->name('subscription.delete');
});
