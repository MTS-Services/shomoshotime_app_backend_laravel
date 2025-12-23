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

Route::name('me.')->prefix('me')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/', 'me')->name('me'); //->middleware(CheckToken::class) add when using scope with passport;
        Route::get('/profile', 'profile')->name('profile'); //->middleware(CheckToken::class) add when using scope with passport;
        Route::get('/company-info', 'companyInfo')->name('company-info'); //->middleware(CheckToken::class) add when using scope with passport;
        Route::post('/update', 'update')->name('update');
        Route::post('/update/company-info', 'updateCompanyInfo')->name('update-company-info');
        Route::post('/change-type', 'changeType')->name('change-type');
        Route::post('/change-password', 'changePassword')->name('change-password');
        Route::post('/toggle-status', 'toggleStatus')->name('toggleStatus');
        Route::delete('/delete', 'delete')->name('delete');
    });
});

Route::controller(PropertyController::class)->name('property.')->prefix('properties')->group(function () {
    Route::get('/all', 'all')->name('all');
    Route::get('/open', 'open')->name('open');
    Route::get('/pending', 'pending')->name('pending');
    Route::get('/archive', 'archive')->name('archive');
    Route::get('/expired', 'expired')->name('expired');
    Route::get('/sold', 'sold')->name('sold');
    Route::get('/deleted', 'deleted')->name('deleted');
    Route::get('/myAds', 'myAds')->name('myAds');
    Route::get('/myArchivedAds', 'myArchivedAds')->name('myArchivedAds');
    Route::get('/my/{slug}', 'myProperty')->name('myProperty');

    Route::post('/create', 'create')->name('create');
    Route::post('/update/{id}', 'update')->name('update');
    Route::post('/feature/{id}', 'toggleFeature')->name('feature');
    Route::post('/make-archive/{id}', 'makeArchive')->name('make-archive');
    Route::post('/renew/{id}', 'renew')->name('renew');
    Route::delete('/destroy/{id}', 'destroy')->name('destroy');
    Route::delete('/permanent-destroy/{id}', 'permanentDestroy')->name('permanent-destroy');
    Route::post('/update/{property}', 'update')->name('update');
    Route::delete('/destroy/{property}', 'destroy')->name('destroy');
    Route::post('/renew/{property}', 'renewProperty')->name('renew');
});

Route::controller(PackageController::class)->name('package.')->prefix('packages')->group(function () {
    Route::get('/all', 'all')->name('all');
    Route::get('/buy/{package_id}', 'buyPackage')->name('buy');
    Route::post('/payment-response', 'handlePaymentResponse')->name('handle-payment-response');
});

Route::get('conversations', [ConversationController::class, 'index'])->name('conversations.index');
Route::post('conversation/create/{id}', [ConversationController::class, 'createConversation'])->name('conversations.create');

Route::post('message/create', [MessageController::class, 'createMessage'])->name('messages.create');
Route::get('messages', [MessageController::class, 'index'])->name('messages.by-conversation');

Route::get('participants', [ParticipantController::class, 'index'])->name('participants.by-conversation');
Route::post('participant/create', [ParticipantController::class, 'storeParticipant'])->name('participants.create');

Route::get('message-reads', [MessageReadController::class, 'index'])->name('message-reads.index');
Route::post('message-read/create/{id}', [MessageReadController::class, 'storeMessageRead'])->name('message-reads.create');
