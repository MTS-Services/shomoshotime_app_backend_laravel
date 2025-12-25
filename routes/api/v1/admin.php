<?php

use App\Http\Controllers\API\V1\ChatManagement\ConversationController;
use App\Http\Controllers\API\V1\ChatManagement\MessageController;
use App\Http\Controllers\API\V1\ChatManagement\MessageReadController;
use App\Http\Controllers\API\V1\ChatManagement\ParticipantController;
use App\Http\Controllers\API\V1\PackageManagement\PackageController;
use App\Http\Controllers\API\V1\PropertyManagement\PropertyController;
use App\Http\Controllers\API\V1\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\CheckToken;



Route::controller(UserController::class)->group(function () {
    Route::get('/all-users', 'getUsers')->name('all-users');
});

