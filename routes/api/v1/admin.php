<?php

use App\Http\Controllers\API\V1\ContentManagement\ContentController;
use App\Http\Controllers\API\V1\UserController;
use Illuminate\Support\Facades\Route;



Route::controller(UserController::class)->prefix('user')->group(function () {
    Route::post('/all', 'getUsers')->name('all-users');
    Route::post('/single/{id}', 'getUser')->name('single-user');
    Route::post('/status-change', 'statusChange')->name('status-change');
    Route::post('/create', 'store')->name('create-user');
    Route::put('/update/{id}', 'update')->name('user.update');
    Route::delete('/delete/{id}', 'delete')->name('user.delete');
});
Route::controller(ContentController::class)->prefix('content')->group(function () {
    Route::post('/list', 'getContents')->name('list-content');
    Route::post('/create', 'store')->name('create-content');
    Route::put('/update/{id}', 'update')->name('content.update');
    Route::delete('/delete/{id}', 'destroy')->name('content.delete');
});



