<?php

use App\Http\Controllers\API\V1\UserController;
use Illuminate\Support\Facades\Route;



Route::controller(UserController::class)->prefix('user')->group(function () {
    Route::post('/all', 'getUsers')->name('all-users');
    Route::post('/status-change', 'statusChange')->name('status-change');
});

