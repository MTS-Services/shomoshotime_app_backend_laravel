<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\SmsController;
use Illuminate\Support\Facades\Route;


Route::group(['as' => 'f.'], function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');
});

Route::post('/sms', [SmsController::class, 'sendSmsViaService'])->name('sendSms');
