<?php

use App\Http\Controllers\API\V1\ContentManagement\ContentController;
use App\Http\Controllers\API\V1\ContentManagement\FlashCardController;
use App\Http\Controllers\API\V1\UserController;
use App\Models\FlashCard;
use Illuminate\Support\Facades\Route;



Route::controller(UserController::class)->prefix('user')->group(function () {
    Route::post('/all', 'getUsers')->name('all-users');
    Route::post('/single/{id}', 'getUser')->name('single-user');
    Route::post('/status-change', 'statusChange')->name('status-change');
    Route::post('/create', 'store')->name('create-user');
    Route::put('/update/{id}', 'update')->name('user.update');
    Route::delete('/delete/{id}', 'delete')->name('user.delete');
});
Route::prefix('content')->group(function () {
    // Content routes
    Route::controller(ContentController::class)->group(function () {
        Route::post('/list', 'getContents')->name('content.list');
        Route::post('/create', 'store')->name('content.create');
        Route::put('/update/{id}', 'update')->name('content.update');
        Route::delete('/delete/{id}', 'destroy')->name('content.delete');
    });

    // Flash Card routes
    Route::controller(FlashCardController::class)->prefix('flash-card')->group(function () {
        Route::post('/list', 'getFlashCards')->name('flash-card.list');
    });

});


