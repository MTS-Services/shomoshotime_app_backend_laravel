<?php

use App\Http\Controllers\API\V1\UserPanel\ContentController;
use Illuminate\Support\Facades\Route;

Route::controller(ContentController::class)->prefix('content')->group(function () {
    Route::post('/study-guides', 'studyGuides')->name('study-guides');
    Route::post('/flash-cards', 'flashCards')->name('flash-cards');
    // Route::post('/single/{id}', 'getUser')->name('single-user');
    // Route::post('/status-change', 'statusChange')->name('status-change');
    // Route::post('/create', 'store')->name('create-user');
    // Route::put('/update/{id}', 'update')->name('user.update');
    // Route::delete('/delete/{id}', 'delete')->name('user.delete');
});