<?php

use App\Http\Controllers\API\V1\AreaController;
use App\Http\Controllers\API\V1\PackageManagement\SubscriptionPlanController;
use App\Http\Controllers\API\V1\AgentController;
use App\Http\Controllers\API\V1\NotificationController;
use App\Http\Controllers\API\V1\PropertyManagement\PropertyController;
use App\Http\Controllers\API\V1\PropertyManagement\CategoryController;
use App\Http\Controllers\API\V1\PropertyManagement\PropertyTypeController;
use Illuminate\Support\Facades\Route;


// START PUBLIC ROUTES
Route::get('areas', [AreaController::class, 'areas'])->name('areas');


Route::name('pm.')->group(function () {
    Route::get('/property-types', [PropertyTypeController::class, 'propertyTypes'])->name('property-types');
    Route::get('/categories', [CategoryController::class, 'categories'])->name('categories');

    Route::controller(PropertyController::class)->name('properties.')->prefix('properties')->group(function () {
        Route::get('/', 'publicProperties')->name('publicProperties');
        Route::get('/{id}', 'publicProperty')->name('publicProperty');
        Route::post('/view/{id}', 'view')->name('view');
    });

    Route::controller(AgentController::class)->name('agents')->prefix('agents')->group(function () {
        Route::get('/', 'agents')->name('agents');
        Route::get('/{id}', 'agent')->name('agent');
    });
});

Route::post('/send-notification', [NotificationController::class, 'sendNotification']);
Route::post('/send-notification-multiple', [NotificationController::class, 'sendToMultiple']);
Route::post('/send-notification-topic', [NotificationController::class, 'sendToTopic']);
