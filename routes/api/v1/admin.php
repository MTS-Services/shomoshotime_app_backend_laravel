<?php

use App\Http\Controllers\API\V1\UserController;
use Illuminate\Support\Facades\Route;



Route::controller(UserController::class)->prefix('user')->group(function () {
    Route::post('/all', 'getUsers')->name('all-users');
    Route::post('/status-change', 'statusChange')->name('status-change');
    Route::post('/create', 'store')->name('create-user');
    Route::put('/update/{id}', 'update')->name('user.update');
    Route::delete('/delete/{id}', 'delete')->name('user.delete');
});

